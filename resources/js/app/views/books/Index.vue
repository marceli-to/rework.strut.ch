<script setup>
import { useBookStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
]

const searchable = (row) => [row.title, row.description]
</script>

<template>
	<ResourceIndex title="Bücher" :store="useBookStore()" :columns="columns" :routes="{ create: 'books.create', edit: 'books.edit' }" createLabel="Neues Buch" sortable :searchable="searchable" searchPlaceholder="Titel oder Angaben" />
</template>
