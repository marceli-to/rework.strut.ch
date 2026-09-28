<script setup>
import { PhTrash, PhPencil, PhStar, PhImage, PhCrop, PhArrowSquareOut, PhDesktop, PhDeviceMobile, PhFilePdf } from '@phosphor-icons/vue'

defineProps({
	media: { type: Object, required: true },
	realFormat: { type: Boolean, default: false },
	showInfo: { type: Boolean, default: true },
	badge: { type: String, default: null },
	hasTeaser: { type: Boolean, default: false },
	isTeaser: { type: Boolean, default: false },
	hasOg: { type: Boolean, default: false },
	isOg: { type: Boolean, default: false },
	showOverlay: { type: Boolean, default: true },
	hasCrop: { type: Boolean, default: false },
	hasEdit: { type: Boolean, default: true },
	hasVariant: { type: Boolean, default: false },
})

const emit = defineEmits(['edit', 'delete', 'teaser', 'og', 'crop', 'click', 'variant'])

function formatSize(bytes) {
	if (!bytes) return '–'
	if (bytes < 1024) return bytes + ' B'
	if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
	return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}
</script>

<template>
	<div
		class="group relative bg-white dark:bg-warm-800 border border-gray-200 dark:border-warm-700 rounded-md overflow-hidden"
		@click="emit('click', media)"
	>
		<!-- Image -->
		<div class="aspect-square overflow-hidden flex items-center justify-center" :class="realFormat ? 'p-16' : ''">
			<video v-if="media.mime_type?.startsWith('video/')" :src="media.original_url" muted playsinline preload="metadata" class="w-full h-full object-cover" />
			<div v-else-if="!media.thumbnail_url" class="flex flex-col items-center gap-8 text-gray-400 dark:text-warm-500">
				<PhFilePdf :size="32" weight="light" />
				<span class="text-xs uppercase">{{ media.original_name?.split('.').pop() }}</span>
			</div>
			<img
				v-else
				:src="realFormat ? media.preview_url : media.thumbnail_url"
				:alt="media.alt || media.original_name"
				:class="realFormat ? 'max-w-full max-h-full object-contain' : 'w-full h-full object-cover'"
			/>
		</div>

		<!-- Info -->
		<div v-if="showInfo" class="px-10 py-8 border-t border-gray-100 dark:border-warm-700">
			<div class="text-xs text-gray-900 dark:text-warm-100 truncate">{{ media.original_name }}</div>
			<div class="text-xs text-gray-400 dark:text-warm-500 mt-2"><template v-if="media.width">{{ media.width }}&times;{{ media.height }} · </template>{{ formatSize(media.size) }}</div>
			<div v-if="badge || (hasVariant && media.variant) || isOg" class="flex items-center gap-4 mt-6">
				<span v-if="badge" class="text-[0.6875rem] font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-full px-8 py-3 leading-none">{{ badge }}</span>
				<span v-if="hasVariant && media.variant" class="text-[0.6875rem] font-medium rounded-full px-8 py-3 leading-none" :class="media.variant === 'mobile' ? 'text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950 border border-violet-200 dark:border-violet-800' : 'text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950 border border-sky-200 dark:border-sky-800'">{{ media.variant === 'mobile' ? 'Mobile' : 'Desktop' }}</span>
				<span v-if="isOg" class="text-[0.6875rem] font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-full px-8 py-3 leading-none">OG</span>
			</div>
		</div>

		<!-- Overlay actions: bar slides down from top on hover -->
		<div
			v-if="showOverlay"
			class="absolute inset-x-8 top-8 -translate-y-[calc(100%+8px)] group-hover:translate-y-0 transition-transform duration-150 rounded-md overflow-hidden"
		>
			<div class="bg-black/80 px-10 py-8 flex items-center justify-around">
				<a :href="media.preview_url || media.original_url" target="_blank" class="text-white/70 hover:text-white" title="In neuem Tab öffnen" @click.stop>
					<PhArrowSquareOut :size="15" weight="light" />
				</a>
				<button v-if="hasEdit" type="button" class="text-white/70 hover:text-white cursor-pointer" title="Bearbeiten" @click.stop="emit('edit', media)">
					<PhPencil :size="15" weight="light" />
				</button>
				<button v-if="hasCrop" type="button" class="text-white/70 hover:text-white cursor-pointer" title="Zuschneiden" @click.stop="emit('crop', media)">
					<PhCrop :size="15" weight="light" />
				</button>
				<button v-if="hasVariant" type="button" class="text-white/70 hover:text-white cursor-pointer" :title="media.variant === 'mobile' ? 'Auf Desktop setzen' : 'Auf Mobile setzen'" @click.stop="emit('variant', media)">
					<PhDeviceMobile v-if="media.variant === 'desktop'" :size="15" weight="light" />
					<PhDesktop v-else :size="15" weight="light" />
				</button>
				<button v-if="hasTeaser" type="button" class="text-white/70 hover:text-white cursor-pointer" title="Als Teaser setzen" @click.stop="emit('teaser', media)">
					<PhStar :size="15" :weight="isTeaser ? 'fill' : 'light'" />
				</button>
				<button v-if="hasOg" type="button" class="text-white/70 hover:text-white cursor-pointer" title="Als Opengraph Image setzen" @click.stop="emit('og', media)">
					<PhImage :size="15" :weight="isOg ? 'fill' : 'light'" />
				</button>
				<button type="button" class="text-white/70 hover:text-white cursor-pointer" title="Löschen" @click.stop="emit('delete', media)">
					<PhTrash :size="15" weight="light" />
				</button>
			</div>
		</div>
	</div>
</template>
