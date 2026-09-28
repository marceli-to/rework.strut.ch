// Masonry grid (replaces the legacy Packery 2 setup on Über uns and Bücher).
// Port of Packery's placement so items land exactly where they did before:
// free spaces sorted top to bottom, then left to right; each item goes into
// the first space it fits (1px tolerance). The gutter is added to every item's
// size, x is set as a percentage, the grid height is the lowest edge minus
// the gutter.
//
// Layout runs once all images and web fonts have loaded, and again when the grid width
// changes. After a toggle inside the grid (modules/toggle.js) the items are
// re-packed keeping their columns (Packery "shiftLayout", as legacy did).

import { debounce } from './debounce';

const GUTTER = 24;

class Rect {
	constructor({ x = 0, y = 0, width = 0, height = 0 }) {
		Object.assign(this, { x, y, width, height });
	}

	contains(rect) {
		return this.x <= rect.x && this.y <= rect.y
			&& this.x + this.width >= rect.x + rect.width
			&& this.y + this.height >= rect.y + rect.height;
	}

	overlaps(rect) {
		return this.x < rect.x + rect.width && this.x + this.width > rect.x
			&& this.y < rect.y + rect.height && this.y + this.height > rect.y;
	}

	canFit(rect) {
		return this.width >= rect.width - 1 && this.height >= rect.height - 1;
	}

	// The free rects left around `rect`, or false if it does not overlap.
	freeRectsAround(rect) {
		if (!this.overlaps(rect)) return false;

		const right = this.x + this.width;
		const bottom = this.y + this.height;
		const rectRight = rect.x + rect.width;
		const rectBottom = rect.y + rect.height;
		const rects = [];

		if (this.y < rect.y) rects.push(new Rect({ x: this.x, y: this.y, width: this.width, height: rect.y - this.y }));
		if (right > rectRight) rects.push(new Rect({ x: rectRight, y: this.y, width: right - rectRight, height: this.height }));
		if (bottom > rectBottom) rects.push(new Rect({ x: this.x, y: rectBottom, width: this.width, height: bottom - rectBottom }));
		if (this.x < rect.x) rects.push(new Rect({ x: this.x, y: this.y, width: rect.x - this.x, height: this.height }));

		return rects;
	}
}

// Removes spaces contained in another space (same order of checks as Packery).
function mergeRects(rects) {
	let i = 0;
	let rect = rects[i];
	outer: while (rect) {
		let j = 0;
		let other = rects[i + j];
		while (other) {
			if (other === rect) {
				j++;
			} else if (other.contains(rect)) {
				rects.splice(i, 1);
				rect = rects[i];
				continue outer;
			} else if (rect.contains(other)) {
				rects.splice(i + j, 1);
			} else {
				j++;
			}
			other = rects[i + j];
		}
		i++;
		rect = rects[i];
	}
	return rects;
}

class Packer {
	constructor(width) {
		this.width = width;
		this.spaces = [new Rect({ width, height: Infinity })];
	}

	// First free space the rect fits in.
	pack(rect) {
		const space = this.spaces.find((s) => s.canFit(rect));
		if (!space) return;
		rect.x = space.x;
		rect.y = space.y;
		this.placed(rect);
	}

	// Keeps rect.x, finds the highest space at that column.
	columnPack(rect) {
		const space = this.spaces.find((s) => s.x <= rect.x && s.x + s.width >= rect.x + rect.width && s.height >= rect.height - 0.01);
		if (!space) return;
		rect.y = space.y;
		this.placed(rect);
	}

	placed(rect) {
		this.spaces = mergeRects(this.spaces.flatMap((space) => space.freeRectsAround(rect) || [space]))
			.sort((a, b) => a.y - b.y || a.x - b.x);
	}
}

function outerSize(el) {
	const style = getComputedStyle(el);
	const px = (value) => parseFloat(value) || 0;
	return {
		width: px(style.width) + px(style.marginLeft) + px(style.marginRight),
		height: px(style.height) + px(style.marginTop) + px(style.marginBottom),
	};
}

function imagesLoaded(el) {
	return Promise.all([...el.querySelectorAll('img')].map((img) => (img.complete
		? null
		: new Promise((resolve) => { img.addEventListener('load', resolve, { once: true }); img.addEventListener('error', resolve, { once: true }); }))));
}

function createMasonry(grid) {
	const items = [...grid.querySelectorAll(':scope > [data-masonry-item]')];
	const rects = new Map();
	let width = 0;

	// Packery: outer width for the percentages, inner width for the packing.
	const measure = () => {
		const style = getComputedStyle(grid);
		const outer = parseFloat(style.width);
		const inner = outer - ['paddingLeft', 'paddingRight', 'borderLeftWidth', 'borderRightWidth'].reduce((sum, key) => sum + parseFloat(style[key]), 0);
		return { outer, inner };
	};

	const layout = (shift = false) => {
		const { outer, inner } = measure();
		width = inner;
		const packer = new Packer(width + GUTTER);
		let maxY = 0;

		for (const item of items) {
			const size = outerSize(item);
			const rect = rects.get(item) ?? new Rect({});
			rect.width = Math.min(size.width + GUTTER, packer.width);
			rect.height = size.height + GUTTER;
			shift ? packer.columnPack(rect) : packer.pack(rect);
			rects.set(item, rect);
			maxY = Math.max(maxY, rect.y + rect.height);

			item.style.position = 'absolute';
			item.style.left = `${(rect.x / outer) * 100}%`;
			item.style.top = `${rect.y}px`;
		}

		grid.style.height = `${Math.max(maxY - GUTTER, 0)}px`;
	};

	grid.style.position = 'relative';
	layout();

	window.addEventListener('resize', debounce(() => {
		if (measure().inner !== width) layout();
	}, 100));

	grid.addEventListener('toggle:change', () => layout(true));
}

export function initMasonry(root = document) {
	root.querySelectorAll('[data-masonry]').forEach((grid) => {
		Promise.all([imagesLoaded(grid), document.fonts.ready]).then(() => createMasonry(grid));
	});
}
