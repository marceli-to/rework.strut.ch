<script setup>
import { useTeamStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

const columns = [
	{ key: 'full_name', label: 'Name', primary: true },
	{ key: 'role', label: 'Funktion' },
	{ key: 'email', label: 'E-Mail' },
]
</script>

<template>
	<ResourceIndex title="Team" :store="useTeamStore()" :columns="columns" :routes="{ create: 'team.create', edit: 'team.edit' }" createLabel="Neues Mitglied" sortable :rowLabel="row => row.full_name" />
</template>
