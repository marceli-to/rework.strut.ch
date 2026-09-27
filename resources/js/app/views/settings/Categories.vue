<script setup>
import { useCategoryStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'
import SettingsTabs from '@/components/layout/SettingsTabs.vue'

const columns = [{ key: 'name', label: 'Kategorie', primary: true }]
</script>

<template>
	<ResourceIndex title="Einstellungen" :store="useCategoryStore()" :columns="columns" :routes="{ create: 'categories.create', edit: 'categories.edit' }" createLabel="Neue Kategorie" sortable>
		<template #before>
			<SettingsTabs />
		</template>
		<template #cell-name="{ row }">
			<router-link :to="{ name: 'categories.edit', params: { id: row.uuid } }" class="hover:underline">{{ row.name }}</router-link>
			<span class="text-gray-400 dark:text-warm-500 ml-8">{{ row.types.length }} Typen</span>
		</template>
	</ResourceIndex>
</template>
