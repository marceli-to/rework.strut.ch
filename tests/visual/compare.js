// Visual comparison: legacy site (ref) vs rework (act).
//
//   node tests/visual/compare.js [--page=home,presse] [--vp=375,1440]
//                                [--all-projects] [--no-states] [--concurrency=4]
//
// Full-page screenshots with fonts loaded, animations off and lazy images
// forced, diffed with pixelmatch. Output: tests/visual/output/{page}/{vp}[-state]-{ref,act,diff}.png
// and tests/visual/output/report.md.

import { chromium } from 'playwright';
import pixelmatch from 'pixelmatch';
import { PNG } from 'pngjs';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { execSync } from 'node:child_process';
import { viewports, sites, pages, sampleProjects, projectPage, globalStates } from './pages.js';

const root = path.dirname(fileURLToPath(import.meta.url));
const outDir = path.join(root, 'output');

const args = Object.fromEntries(process.argv.slice(2).map((a) => {
	const [k, v] = a.replace(/^--/, '').split('=');
	return [k, v ?? true];
}));
const list = (v) => (typeof v === 'string' ? v.split(',') : null);

const projectIds = args['all-projects']
	? execSync(`php artisan tinker --execute="echo App\\\\Models\\\\Project::where('has_detail', 1)->orderBy('id')->pluck('id')->join(',');"`, { cwd: path.join(root, '../..') }).toString().trim().split(',').map(Number)
	: sampleProjects;

let targets = [...pages, ...projectIds.map(projectPage)];
if (list(args.page)) targets = targets.filter((p) => list(args.page).includes(p.key));
const vps = list(args.vp)?.map(Number) ?? viewports;
const withStates = !args['no-states'];

const STABILIZE_CSS = `
	*, *::before, *::after { transition: none !important; animation: none !important; caret-color: transparent !important; }
	html { scroll-behavior: auto !important; }
`;

async function prepare(page) {
	await page.addStyleTag({ content: STABILIZE_CSS });
	await page.evaluate(async () => {
		document.querySelectorAll('img[loading="lazy"]').forEach((img) => { img.loading = 'eager'; });
		document.querySelectorAll('video').forEach((v) => { v.pause(); v.currentTime = 0; });
		// Scroll through the page so scroll-triggered images load.
		const step = window.innerHeight;
		for (let y = 0; y < document.body.scrollHeight; y += step) {
			window.scrollTo(0, y);
			await new Promise((r) => setTimeout(r, 50));
		}
		window.scrollTo(0, 0);
		await document.fonts.ready;
		await Promise.all([...document.images].map((img) => (img.complete ? null : new Promise((r) => { img.onload = img.onerror = r; }))));
	});
	// Let scroll-dependent header classes settle back to the top state.
	await page.waitForTimeout(400);
}

async function shoot(context, site, target, vp, state) {
	const page = await context.newPage();
	await page.setViewportSize({ width: vp, height: 900 });
	const response = await page.goto(sites[site] + target.path, { waitUntil: 'networkidle' });
	await prepare(page);
	if (state) {
		await state[site](page);
		await page.waitForTimeout(300);
	}
	const mask = (target.mask?.[site] ?? []).map((s) => page.locator(s));
	const buffer = await page.screenshot({ fullPage: true, mask, maskColor: '#ff00ff', animations: 'disabled' });
	await page.close();
	return { buffer, status: response?.status() };
}

// Pads both images to the same size; the padding counts as difference.
function pad(png, width, height) {
	if (png.width === width && png.height === height) return png;
	const out = new PNG({ width, height });
	out.data.fill(0);
	PNG.bitblt(png, out, 0, 0, png.width, png.height, 0, 0);
	return out;
}

function diff(refBuf, actBuf) {
	const ref = PNG.sync.read(refBuf);
	const act = PNG.sync.read(actBuf);
	const width = Math.max(ref.width, act.width);
	const height = Math.max(ref.height, act.height);
	const out = new PNG({ width, height });
	const pixels = pixelmatch(pad(ref, width, height).data, pad(act, width, height).data, out.data, width, height, { threshold: 0.1 });
	return {
		png: PNG.sync.write(out),
		percent: (pixels / (width * height)) * 100,
		size: { ref: `${ref.width}×${ref.height}`, act: `${act.width}×${act.height}` },
	};
}

async function run() {
	const browser = await chromium.launch();
	const context = await browser.newContext({ ignoreHTTPSErrors: true, deviceScaleFactor: 1, reducedMotion: 'reduce' });

	const jobs = [];
	for (const target of targets) {
		const states = [null];
		if (withStates) {
			states.push(...(target.states ?? []));
			if (target.key === 'home') states.push(...globalStates);
		}
		for (const vp of vps) {
			for (const state of states) {
				if (state?.viewports && !state.viewports.includes(vp)) continue;
				jobs.push({ target, vp, state });
			}
		}
	}

	const results = [];
	const concurrency = Number(args.concurrency ?? 4);
	let next = 0;
	await Promise.all(Array.from({ length: concurrency }, async () => {
		while (next < jobs.length) {
			const { target, vp, state } = jobs[next++];
			const name = `${vp}${state ? `-${state.name}` : ''}`;
			const dir = path.join(outDir, target.key);
			await fs.mkdir(dir, { recursive: true });
			try {
				const ref = await shoot(context, 'ref', target, vp, state);
				const act = await shoot(context, 'act', target, vp, state);
				const d = diff(ref.buffer, act.buffer);
				await fs.writeFile(path.join(dir, `${name}-ref.png`), ref.buffer);
				await fs.writeFile(path.join(dir, `${name}-act.png`), act.buffer);
				await fs.writeFile(path.join(dir, `${name}-diff.png`), d.png);
				results.push({ page: target.key, vp, state: state?.name ?? '', percent: d.percent, size: d.size, status: `${ref.status}/${act.status}` });
				console.log(`${target.key.padEnd(20)} ${name.padEnd(18)} ${d.percent.toFixed(3)} %`);
			} catch (e) {
				results.push({ page: target.key, vp, state: state?.name ?? '', error: e.message.split('\n')[0] });
				console.log(`${target.key.padEnd(20)} ${name.padEnd(18)} ERROR ${e.message.split('\n')[0]}`);
			}
		}
	}));

	await browser.close();
	await writeReport(results);
}

async function writeReport(results) {
	results.sort((a, b) => a.page.localeCompare(b.page) || a.vp - b.vp || a.state.localeCompare(b.state));
	const lines = [
		'# Visual comparison report',
		'',
		`Generated ${new Date().toISOString()} · ref ${sites.ref} · act ${sites.act}`,
		'',
		'| Page | Viewport | State | Diff % | Size ref | Size act | HTTP ref/act |',
		'|---|---|---|---|---|---|---|',
		...results.map((r) => (r.error
			? `| ${r.page} | ${r.vp} | ${r.state} | **error** | | | ${r.error} |`
			: `| ${r.page} | ${r.vp} | ${r.state} | ${r.percent.toFixed(3)} | ${r.size.ref} | ${r.size.act} | ${r.status} |`)),
	];
	await fs.writeFile(path.join(outDir, 'report.md'), lines.join('\n') + '\n');
	const failed = results.filter((r) => r.error || r.percent > 0).length;
	console.log(`\n${results.length} comparisons, ${failed} with differences → tests/visual/output/report.md`);
}

run();
