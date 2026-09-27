<script setup>
import { computed } from 'vue'

/**
 * Small SVG preview generated from a layout spec (config/grids.php) –
 * no hand-drawn icon per layout.
 */
const props = defineProps({
	layout: { type: Object, required: true },
	width: { type: Number, default: 36 },
})

const gap = 2
const rects = computed(() => {
	const columns = props.layout.columns
	if (!columns.length) {
		// slideshow: three overlapping frames
		return [0, 1, 2].map(i => ({ x: 2 + i * 3, y: 2 + i * 2, w: props.width - 12, h: 14, fill: i === 2 }))
	}

	const total = columns.reduce((sum, c) => sum + c.fr, 0)
	const inner = props.width - gap * (columns.length - 1)
	const unit = inner / total
	const height = props.width * 2 / 3
	const out = []
	let x = 0

	for (const column of columns) {
		const w = column.fr * unit
		const cells = column.cells
		const weights = cells.map(c => c.ratio ?? (c.size === 'spacer' ? 0 : 50))
		const sum = weights.reduce((a, b) => a + b, 0) || 1
		const available = height - gap * (cells.length - 1)
		let y = 0
		cells.forEach((cell, i) => {
			const h = cell.size === 'spacer' ? available - y : (weights[i] / sum) * available
			if (cell.size !== 'spacer') out.push({ x, y, w, h, fill: true })
			y += h + gap
		})
		x += w + gap
	}

	return out
})
</script>

<template>
	<svg :width="width" :height="width * 2 / 3" :viewBox="`0 0 ${width} ${width * 2 / 3}`" class="shrink-0" aria-hidden="true">
		<rect
			v-for="(r, i) in rects"
			:key="i"
			:x="r.x" :y="r.y" :width="r.w" :height="r.h" rx="1"
			:class="r.fill ? 'fill-current' : 'fill-none stroke-current'"
		/>
	</svg>
</template>
