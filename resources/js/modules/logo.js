export function initLogo(root = document) {
  root.querySelectorAll('[data-logo]').forEach((el) => {
    requestAnimationFrame(() => el.classList.add('is-ready'));
  });
}
