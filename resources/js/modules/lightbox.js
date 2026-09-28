// Lightbox (replaces the legacy fancyBox 3 setup), markup in x-site.lightbox.
// Links with data-lightbox="single" open alone, data-lightbox="gallery" links
// form one group per page. Geometry and timing follow fancyBox 3:
// - the image fits the viewport minus 44px above and below (6px when the
//   viewport is at most 576px high), never enlarged, centred (rounded down);
// - it zooms from its thumbnail on open and back on close, slides fade,
//   all in 366ms; the white background fades in with it.
// Keyboard: Escape closes (native <dialog>), arrow keys browse. Touch: swipe.

const DURATION = 366;

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const nextFrame = () => new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));

function load(src) {
	return new Promise((resolve) => {
		const img = new Image();
		img.onload = img.onerror = () => resolve(img);
		img.src = src;
	});
}

// fancyBox 3 "getFitPos" for an image slide.
function fit(natural, viewport) {
	const padding = viewport.height <= 576 ? 6 : 44;
	const maxWidth = viewport.width;
	const maxHeight = viewport.height - 2 * padding;
	const ratio = Math.min(1, maxWidth / natural.width, maxHeight / natural.height);
	let width = natural.width * ratio;
	let height = natural.height * ratio;
	if (width > maxWidth - 0.5) width = maxWidth;
	if (height > maxHeight - 0.5) height = maxHeight;

	return {
		left: Math.floor(Math.max(0, (maxWidth - width) / 2)),
		top: Math.floor(Math.max(0, (maxHeight - height) / 2) + padding),
		width,
		height,
	};
}

export function initLightbox(root = document) {
	const dialog = root.querySelector('[data-lightbox-dialog]');
	if (!dialog) return;

	const bg = dialog.querySelector('[data-lightbox-bg]');
	const stage = dialog.querySelector('[data-lightbox-stage]');
	const prev = dialog.querySelector('[data-lightbox-prev]');
	const next = dialog.querySelector('[data-lightbox-next]');
	const template = dialog.querySelector('[data-lightbox-slide]');

	let group = [];
	let index = 0;
	let slide = null;
	let busy = false;

	const viewport = () => ({ width: dialog.clientWidth, height: dialog.clientHeight });
	const thumbnail = (link) => link.querySelector('img');

	const place = (figure) => {
		const pos = fit(figure._natural, viewport());
		Object.assign(figure.style, { left: `${pos.left}px`, top: `${pos.top}px`, width: `${pos.width}px`, height: `${pos.height}px` });
		return pos;
	};

	const createSlide = async (link) => {
		const figure = template.content.firstElementChild.cloneNode(true);
		const img = figure.querySelector('img');
		const loaded = await load(link.href);
		figure._natural = { width: loaded.naturalWidth || 1, height: loaded.naturalHeight || 1 };
		img.src = link.href;
		img.alt = thumbnail(link)?.alt ?? '';
		figure.querySelector('figcaption').textContent = link.dataset.caption ?? '';
		return figure;
	};

	// Transform that puts the slide over the thumbnail (for the zoom).
	const fromThumbnail = (figure, link) => {
		const thumb = thumbnail(link)?.getBoundingClientRect();
		const pos = figure.getBoundingClientRect();
		if (!thumb || !thumb.width || !pos.width) return null;
		return `translate(${thumb.left - pos.left}px, ${thumb.top - pos.top}px) scale(${thumb.width / pos.width}, ${thumb.height / pos.height})`;
	};

	const updateArrows = () => {
		const isGallery = group.length > 1;
		prev.hidden = next.hidden = !isGallery;
		prev.toggleAttribute('data-inactive', index === 0);
		next.toggleAttribute('data-inactive', index === group.length - 1);
	};

	const open = async (link) => {
		if (busy) return;
		busy = true;
		const mode = link.dataset.lightbox;
		group = mode === 'gallery' ? [...document.querySelectorAll('[data-lightbox="gallery"]')] : [link];
		index = group.indexOf(link);

		slide = await createSlide(link);
		stage.replaceChildren(slide);
		updateArrows();
		dialog.showModal();
		place(slide);

		const transform = fromThumbnail(slide, link);
		if (transform) slide.style.transform = transform;
		await nextFrame();
		slide.style.transition = `transform ${DURATION}ms ease`;
		slide.style.transform = '';
		bg.setAttribute('data-open', '');
		await wait(DURATION);
		slide.style.transition = '';
		busy = false;
	};

	const close = async () => {
		if (busy || !dialog.open) return;
		busy = true;
		const transform = fromThumbnail(slide, group[index]);
		slide.style.transition = `transform ${DURATION}ms ease, opacity ${DURATION}ms`;
		if (transform) slide.style.transform = transform;
		else slide.style.opacity = '0';
		bg.removeAttribute('data-open');
		await wait(DURATION);
		dialog.close();
		stage.replaceChildren();
		busy = false;
	};

	const show = async (to) => {
		if (busy || to < 0 || to >= group.length || to === index) return;
		busy = true;
		index = to;
		updateArrows();
		const incoming = await createSlide(group[index]);
		incoming.style.opacity = '0';
		stage.append(incoming);
		place(incoming);
		await nextFrame();
		incoming.style.opacity = '1';
		slide.style.opacity = '0';
		await wait(DURATION);
		slide.remove();
		slide = incoming;
		busy = false;
	};

	document.addEventListener('click', (event) => {
		const link = event.target.closest('a[data-lightbox]');
		if (!link || event.metaKey || event.ctrlKey || event.shiftKey) return;
		event.preventDefault();
		open(link);
	});

	dialog.querySelector('[data-lightbox-close]').addEventListener('click', close);
	prev.addEventListener('click', () => show(index - 1));
	next.addEventListener('click', () => show(index + 1));

	// Outside the image (single view): close, as fancyBox did.
	stage.addEventListener('click', (event) => {
		if (!event.target.closest('figure')) close();
	});

	dialog.addEventListener('cancel', (event) => {
		event.preventDefault();
		close();
	});

	dialog.addEventListener('keydown', (event) => {
		if (event.key === 'ArrowLeft') show(index - 1);
		if (event.key === 'ArrowRight') show(index + 1);
	});

	// The page behind stays put.
	dialog.addEventListener('wheel', (event) => event.preventDefault(), { passive: false });

	let touchX = null;
	dialog.addEventListener('touchstart', (event) => { touchX = event.touches[0].clientX; }, { passive: true });
	dialog.addEventListener('touchmove', (event) => event.preventDefault(), { passive: false });
	dialog.addEventListener('touchend', (event) => {
		if (touchX === null) return;
		const dx = event.changedTouches[0].clientX - touchX;
		touchX = null;
		if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
	});

	window.addEventListener('resize', () => {
		if (dialog.open) stage.querySelectorAll('figure').forEach(place);
	});
}
