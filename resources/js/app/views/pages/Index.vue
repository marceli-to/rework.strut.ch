<script setup>
import { usePageStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'meta_description', label: 'Meta Description', class: 'max-w-[32rem] truncate' },
]
</script>

<template>
	<ResourceIndex title="Seiten" :store="usePageStore()" :columns="columns" :routes="{ edit: 'pages.edit' }" :deletable="false" />
</template>
