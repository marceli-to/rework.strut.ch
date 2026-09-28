import { chromium } from 'playwright';
const b = await chromium.launch();
for (const path of ['/', '/bauten/1', '/bauten/2', '/ueber-uns', '/buecher']) {
  const row = [];
  for (const [host, match] of [['https://strut.ch.test', '/storage/media/'], ['https://rework.strut.ch.test', '/img/uploads/']]) {
    const p = await b.newPage({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
    await p.goto(host + path); await p.waitForLoadState('networkidle');
    await p.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 700) { scrollTo(0, y); await new Promise(r => setTimeout(r, 60)); } }); await p.waitForLoadState('networkidle');
    const [n, c] = await p.evaluate((m) => { const e = performance.getEntriesByType('resource').filter(r => r.name.includes(m)); return [e.reduce((s, r) => s + r.decodedBodySize, 0), e.length]; }, match);
    row.push(`${(n / 1048576).toFixed(2)} MB/${c}`); await p.close();
  }
  console.log(path.padEnd(11), 'legacy', row[0].padEnd(12), 'new', row[1]);
}
await b.close();
