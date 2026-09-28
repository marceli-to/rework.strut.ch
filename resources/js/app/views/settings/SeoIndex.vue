<script setup>
import { useSeoStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

// SEO of the listing pages (the content pages have theirs in "Seiten")
const columns = [
	{ key: 'title', label: 'Seite', primary: true },
	{ key: 'meta_description', label: 'Meta Description', limit: 60 },
	{ key: 'og', label: 'Opengraph Image', class: 'w-100' },
]

const ogImage = (row) => row.media?.find(m => m.collection === 'og')
</script>

<template>
	<ResourceIndex title="SEO" :store="useSeoStore()" :columns="columns" :routes="{ edit: 'seo.edit' }" :deletable="false" :togglable="false">
		<template #cell-og="{ row }">
			<img v-if="ogImage(row)?.thumbnail_url" :src="ogImage(row).thumbnail_url" alt="" class="size-24 rounded-sm object-cover">
			<span v-else class="text-gray-300 dark:text-warm-700">–</span>
		</template>
	</ResourceIndex>
</template>
