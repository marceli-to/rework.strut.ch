<script setup>
import { ref, computed, watch } from 'vue'
import { PhFilmStrip } from '@phosphor-icons/vue'
import Drawer from '@/components/ui/drawer/Drawer.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'

/**
 * Choose media (grouped by project) or news for a grid slot.
 */
const props = defineProps({
	open: { type: Boolean, default: false },
	media: { type: Array, default: () => [] },
	news: { type: Array, default: () => [] },
	acceptsNews: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'select'])

const tab = ref('media')
const search = ref('')
const hidePlaced = ref(false)

watch(() => props.open, open => {
	if (open) tab.value = 'media'
})

const groups = computed(() => {
	const term = search.value.toLowerCase()
	const map = new Map()
	for (const media of props.media) {
		if (hidePlaced.value && media.placed) continue
		const title = media.project?.title ?? ''
		if (term && !title.toLowerCase().includes(term) && !media.original_name.toLowerCase().includes(term)) continue
		if (!map.has(title)) map.set(title, [])
		map.get(title).push(media)
	}
	return [...map.entries()].map(([title, items]) => ({ title, items }))
})

const filteredNews = computed(() => {
	const term = search.value.toLowerCase()
	return props.news.filter(n => !term || n.title.toLowerCase().includes(term))
})
</script>

<template>
	<Drawer :open="open" title="Inhalt wählen" size="lg" @close="emit('close')">
		<div class="px-24 py-16 flex flex-col gap-16">
			<div v-if="acceptsNews" class="flex gap-24 text-xs font-medium uppercase tracking-[0.08em]">
				<button v-for="t in [{ key: 'media', label: 'Bilder & Videos' }, { key: 'news', label: 'News' }]" :key="t.key" type="button"
					class="pb-4 cursor-pointer" :class="tab === t.key ? 'text-gray-900 dark:text-warm-100 border-b border-gray-900 dark:border-warm-200' : 'text-gray-400 dark:text-warm-500'"
					@click="tab = t.key">{{ t.label }}</button>
			</div>

			<div class="flex items-center gap-16">
				<FormInput v-model="search" placeholder="Suchen…" class="flex-1" />
				<FormCheckbox v-if="tab === 'media'" v-model="hidePlaced">Nur unplatzierte</FormCheckbox>
			</div>

			<template v-if="tab === 'media'">
				<div v-if="!groups.length" class="text-sm text-gray-400 dark:text-warm-500">Keine Bilder vorhanden.</div>
				<div v-for="group in groups" :key="group.title">
					<h4 v-if="group.title" class="text-xs text-gray-500 dark:text-warm-400 mb-8">{{ group.title }}</h4>
					<div class="grid grid-cols-4 gap-8">
						<button
							v-for="media in group.items"
							:key="media.uuid"
							type="button"
							class="relative aspect-square overflow-hidden rounded-sm bg-gray-100 dark:bg-warm-800 cursor-pointer hover:ring-2 hover:ring-gray-900 dark:hover:ring-warm-100"
							:title="media.original_name"
							@click="emit('select', { media_id: media.uuid })"
						>
							<video v-if="media.mime_type.startsWith('video/')" :src="media.original_url" muted preload="metadata" class="w-full h-full object-cover" />
							<img v-else :src="media.thumbnail_url" class="w-full h-full object-cover" loading="lazy" />
							<span v-if="media.mime_type.startsWith('video/')" class="absolute left-6 top-6 text-white drop-shadow"><PhFilmStrip :size="14" weight="fill" /></span>
							<span v-if="media.placed" class="absolute right-6 top-6 text-[0.625rem] px-6 py-2 rounded-full bg-white/90 text-gray-700">platziert</span>
						</button>
					</div>
				</div>
			</template>

			<template v-else>
				<button
					v-for="item in filteredNews"
					:key="item.uuid"
					type="button"
					class="text-left px-12 py-10 rounded-md border border-gray-200 dark:border-warm-700 hover:border-gray-900 dark:hover:border-warm-200 cursor-pointer"
					:class="item.publish ? '' : 'opacity-50'"
					@click="emit('select', { news_id: item.uuid })"
				>
					<span class="block text-xs text-gray-400 dark:text-warm-500">{{ item.date_label }}</span>
					<span class="block text-sm text-gray-900 dark:text-warm-100">{{ item.title }}</span>
				</button>
			</template>
		</div>
	</Drawer>
</template>
