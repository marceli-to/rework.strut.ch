// Accessibility audit (axe-core, WCAG 2.1 A/AA) of the public pages.
//
//   node tests/visual/a11y.mjs [--page=home,presse] [--width=375] [--best-practice]
import { chromium } from 'playwright';
import { AxeBuilder } from '@axe-core/playwright';
import { pages, sites, sampleProjects, projectPage } from './pages.js';

const only = process.argv.find((a) => a.startsWith('--page='))?.split('=')[1]?.split(',');
const targets = [...pages, ...sampleProjects.slice(0, 2).map(projectPage)].filter((t) => !only || only.includes(t.key));
const browser = await chromium.launch();
const context = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: Number(process.argv.find((a) => a.startsWith('--width='))?.split('=')[1] ?? 1280), height: 900 } });
const summary = {};

for (const target of targets) {
	const page = await context.newPage();
	const path = typeof target.path === 'string' ? target.path : target.path.act;
	await page.goto(sites.act + path, { waitUntil: 'networkidle' });
	const { violations } = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', ...(process.argv.includes('--best-practice') ? ['best-practice'] : [])]).analyze();
	for (const v of violations) {
		summary[v.id] ??= { impact: v.impact, help: v.help, pages: new Set(), nodes: 0, sample: v.nodes[0]?.target.join(' ') };
		summary[v.id].pages.add(target.key);
		summary[v.id].nodes += v.nodes.length;
	}
	await page.close();
}

for (const [id, v] of Object.entries(summary)) {
	console.log(`${v.impact.padEnd(9)} ${id}: ${v.help} — ${v.nodes} nodes on ${v.pages.size} pages (${[...v.pages].slice(0, 4).join(', ')}) e.g. ${v.sample}`);
}
console.log(Object.keys(summary).length ? '' : 'No violations.');
await browser.close();
