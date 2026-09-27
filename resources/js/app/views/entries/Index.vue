<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useEntryStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'

// Press, awards and lectures share this view; the type comes from the route.
const route = useRoute()
const type = computed(() => route.meta.entryType)

const columns = computed(() => [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'description', label: 'Beschreibung' },
	...(type.value === 'press' ? [{ key: 'project', label: 'Projekt' }] : []),
	{ key: 'year', label: 'Jahr' },
])
</script>

<template>
	<ResourceIndex
		:key="type"
		:title="route.meta.title"
		:store="useEntryStore()"
		:columns="columns"
		:params="{ type }"
		:routes="{ create: `${type}.create`, edit: `${type}.edit` }"
		createLabel="Neuer Eintrag"
	/>
</template>
