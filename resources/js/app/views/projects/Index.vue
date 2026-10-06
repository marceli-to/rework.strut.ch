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

// like the legacy filter: name, category, type, status (plus year)
const searchable = (row) => [
	row.full_title,
	row.year,
	row.status_label,
	row.category_type?.name_singular,
	row.category_type?.name_plural,
	row.category_type?.category?.name,
]
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
		:searchable="searchable"
		searchPlaceholder="Projekt, Jahr, Kategorie, Typ oder Status"
	/>
</template>
