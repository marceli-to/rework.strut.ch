// Show/hide toggles (legacy contact.js, project.js): a [data-toggle] button
// shows or hides the element named in its aria-controls, then fires
// `toggle:change` (bubbles), e.g. for the masonry to re-layout.
// - data-toggle="open": sets data-open on the target instead of `hidden`
//   (for content that stays in the layout on small screens);
// - data-toggle-dismiss: a click outside the target and the button closes it.

export function initToggles(root = document) {
	const targetOf = (button) => document.getElementById(button.getAttribute('aria-controls'));
	const isOpen = (button, target) => (button.dataset.toggle === 'open' ? target.hasAttribute('data-open') : !target.hidden);

	const set = (button, target, open) => {
		if (button.dataset.toggle === 'open') target.toggleAttribute('data-open', open);
		else target.hidden = !open;
		button.setAttribute('aria-expanded', String(open));
		button.dispatchEvent(new CustomEvent('toggle:change', { bubbles: true }));
	};

	root.addEventListener('click', (event) => {
		const button = event.target.closest('[data-toggle]');
		const target = button && targetOf(button);
		if (target) set(button, target, !isOpen(button, target));

		root.querySelectorAll('[data-toggle-dismiss][aria-expanded="true"]').forEach((other) => {
			const otherTarget = targetOf(other);
			if (other !== button && otherTarget && !otherTarget.contains(event.target)) set(other, otherTarget, false);
		});
	});
}
