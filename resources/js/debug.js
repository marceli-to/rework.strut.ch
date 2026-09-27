const KEY = 'debug-grid';
const ICON = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256"><path d="M216,52H40A12,12,0,0,0,28,64V192a12,12,0,0,0,12,12H216a12,12,0,0,0,12-12V64A12,12,0,0,0,216,52ZM100,148V108h56v40Zm56,8v40H100V156ZM36,108H92v40H36Zm64-8V60h56v40Zm64,8h56v40H164Zm56-44v36H164V60h52A4,4,0,0,1,220,64ZM40,60H92v40H36V64A4,4,0,0,1,40,60ZM36,192V156H92v40H40A4,4,0,0,1,36,192Zm180,4H164V156h56v36A4,4,0,0,1,216,196Z"/></svg>';

const style = document.createElement('style');
style.textContent = '.debug-grid{display:none}body.debug-on .debug-grid{display:block}#debug-toggle{position:fixed;top:0px;right:4px;z-index:9999;background:transparent;border:0;padding:4px;cursor:pointer;line-height:0;color:#000;opacity:.35;transition:opacity .15s}#debug-toggle:hover{opacity:1}body.debug-on #debug-toggle{opacity:1}';
document.head.appendChild(style);

const on = localStorage.getItem(KEY) === '1';

function apply(v) {
  document.body.classList.toggle('debug-on', v);
  localStorage.setItem(KEY, v ? '1' : '0');
}

document.addEventListener('DOMContentLoaded', () => {
  apply(on);
  const btn = document.createElement('button');
  btn.id = 'debug-toggle';
  btn.type = 'button';
  btn.setAttribute('aria-label', 'Toggle debug grid');
  btn.innerHTML = ICON;
  btn.addEventListener('click', () => {
    apply(!document.body.classList.contains('debug-on'));
  });
  document.body.appendChild(btn);
});
