<script setup>
import { useJobStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'lead', label: 'Lead' },
]
</script>

<template>
	<ResourceIndex title="Jobs" :store="useJobStore()" :columns="columns" :routes="{ create: 'jobs.create', edit: 'jobs.edit' }" createLabel="Neues Inserat" sortable />
</template>
