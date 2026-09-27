<script setup>
import { PhPlus, PhTrash, PhNewspaper, PhFilmStrip } from '@phosphor-icons/vue'

/**
 * One slot of a grid row. Filled slots can be dragged onto other slots.
 */
const props = defineProps({
	item: { type: Object, default: null },
	ratio: { type: Number, default: 66.6667 },
	acceptsNews: { type: Boolean, default: false },
	dragging: { type: Boolean, default: false },
})

const emit = defineEmits(['add', 'remove', 'dragstart', 'drop'])
</script>

<template>
	<div
		class="group relative w-full rounded-sm overflow-hidden transition-shadow"
		:class="[
			item ? 'bg-gray-100 dark:bg-warm-800 cursor-grab active:cursor-grabbing' : 'border border-dashed border-gray-300 dark:border-warm-600',
			dragging ? 'ring-2 ring-sky-300/60' : '',
		]"
		:style="{ paddingTop: ratio + '%' }"
		:draggable="!!item"
		@dragstart="emit('dragstart', $event)"
		@dragover.prevent
		@drop.prevent="emit('drop')"
	>
		<div class="absolute inset-0">
			<template v-if="item">
				<div v-if="item.type === 'news'" class="h-full flex flex-col justify-center gap-6 p-12 border-y border-gray-900 dark:border-warm-200 bg-white dark:bg-warm-900 text-center">
					<PhNewspaper :size="16" weight="light" class="mx-auto text-gray-400" />
					<span class="text-xs text-gray-400 dark:text-warm-500">{{ item.news.date_label }}</span>
					<span class="text-sm text-gray-900 dark:text-warm-100 line-clamp-3">{{ item.news.title }}</span>
				</div>
				<template v-else>
					<video v-if="item.type === 'video'" :src="item.media.original_url" muted playsinline preload="metadata" class="w-full h-full object-cover" />
					<img v-else :src="item.media.preview_url" :alt="item.media.alt || ''" class="w-full h-full object-cover" draggable="false" />
					<span v-if="item.type === 'video'" class="absolute left-8 top-8 text-white drop-shadow"><PhFilmStrip :size="16" weight="fill" /></span>
					<span v-if="item.media.project" class="absolute inset-x-0 bottom-0 px-8 py-6 text-xs text-white bg-black/50 truncate">{{ item.media.project.title }}</span>
				</template>
				<button
					type="button"
					class="absolute right-8 top-8 p-6 rounded bg-black/70 text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
					title="Entfernen"
					@click="emit('remove')"
				>
					<PhTrash :size="14" weight="light" />
				</button>
			</template>
			<button
				v-else
				type="button"
				class="w-full h-full flex flex-col items-center justify-center gap-4 text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 hover:bg-gray-50 dark:hover:bg-warm-800 transition-colors cursor-pointer"
				@click="emit('add')"
			>
				<PhPlus :size="18" weight="light" />
				<span class="text-xs">{{ acceptsNews ? 'Bild, Video oder News' : 'Bild oder Video' }}</span>
			</button>
		</div>
	</div>
</template>
