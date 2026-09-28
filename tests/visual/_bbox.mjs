import { PNG } from 'pngjs'; import fs from 'node:fs';
for (const f of process.argv.slice(2)) {
  const d = PNG.sync.read(fs.readFileSync(f)); let x0=1e9,y0=1e9,x1=-1,y1=-1,n=0;
  for (let y=0;y<d.height;y++) for (let x=0;x<d.width;x++){ const i=(y*d.width+x)*4; if (d.data[i]===255&&d.data[i+1]<50&&d.data[i+2]<50){n++; x0=Math.min(x0,x);y0=Math.min(y0,y);x1=Math.max(x1,x);y1=Math.max(y1,y);} }
  if (n) console.log(f.split('/').slice(-2).join('/'), n, [x0,y0,x1,y1]);
}
