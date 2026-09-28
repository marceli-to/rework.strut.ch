<script setup>
import { useProjectStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'full_title', label: 'Projekt', primary: true },
	{ key: 'year', label: 'Jahr' },
	{ key: 'status_label', label: 'Status' },
]

// grouped by type like the Werkliste (the API orders by category, type, project);
// the sort order is kept per type, so dragging stays within a group
const byType = (row) => ({ key: row.category_type?.uuid ?? 'none', label: row.category_type?.name_plural ?? 'Ohne Typ' })
</script>

<template>
	<ResourceIndex
		title="Projekte"
		:store="useProjectStore()"
		:columns="columns"
		:routes="{ create: 'projects.create', edit: 'projects.edit' }"
		createLabel="Neues Projekt"
		sortable
		:groupBy="byType"
		:rowLabel="row => row.full_title"
	/>
</template>
