// Counts the diff pixels of a compare.js run that lie outside every image,
// video and slideshow box of the rework page: 0 means the remaining
// differences are only photo re-encoding noise.
//
//   node tests/visual/outside-images.mjs <page-key> <viewport> [...]
//   (run compare.js for those pages/viewports first)
import { pages, sites, sampleProjects, projectPage } from './pages.js';
import { chromium } from 'playwright';
import { PNG } from 'pngjs'; import fs from 'node:fs';
const [page, ...vps] = process.argv.slice(2);
const target = [...pages, ...sampleProjects.map(projectPage)].find((t) => t.key === page) ?? projectPage(Number(page.replace('projekt-', '')));
const path = target.path;
const b = await chromium.launch();
for (const w of vps.map(Number)) {
  const p = await b.newPage({ ignoreHTTPSErrors: true, viewport: { width: w, height: 900 } });
  await p.goto(sites.act + path); await p.waitForLoadState('networkidle');
  const rects = await p.evaluate(() => [...document.querySelectorAll('main img, main video, [data-slideshow]')].map(e => { const r = e.getBoundingClientRect(); return [r.left, r.top + scrollY, r.right, r.bottom + scrollY]; }));
  const d = PNG.sync.read(fs.readFileSync(`tests/visual/output/${page}/${w}-diff.png`));
  let inside = 0, outside = 0; const out = [];
  for (let y = 0; y < d.height; y++) for (let x = 0; x < d.width; x++) { const i = (y * d.width + x) * 4; if (d.data[i] === 255 && d.data[i + 1] < 50 && d.data[i + 2] < 50) { if (rects.some(([l, t, r, bt]) => x >= l - 1 && x <= r + 1 && y >= t - 1 && y <= bt + 1)) inside++; else { outside++; if (out.length < 5) out.push([x, y]); } } }
  console.log(page, w, 'inside images:', inside, 'outside:', outside, out);
  await p.close();
}
await b.close();
