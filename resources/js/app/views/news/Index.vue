<script setup>
import { useNewsStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'date_label', label: 'Datum' },
]
</script>

<template>
	<ResourceIndex title="News" :store="useNewsStore()" :columns="columns" :routes="{ create: 'news.create', edit: 'news.edit' }" createLabel="Neue News" />
</template>
