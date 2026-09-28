<script setup>
import { useCategoryStore } from '@/stores/resources'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const columns = [
	{ key: 'name', label: 'Kategorie', primary: true },
	{ key: 'types', label: 'Typen' },
]
</script>

<template>
	<ResourceIndex title="Kategorien" :store="useCategoryStore()" :columns="columns" :routes="{ create: 'categories.create', edit: 'categories.edit' }" createLabel="Neue Kategorie" sortable>
		<template #cell-name="{ row }">
			<router-link :to="{ name: 'categories.edit', params: { id: row.uuid } }" class="hover:underline">{{ row.name }}</router-link>
		</template>
		<template #cell-types="{ row }">
			<div class="flex flex-wrap gap-4">
				<Badge v-for="type in row.types" :key="type.uuid">{{ type.name_singular }}</Badge>
			</div>
		</template>
	</ResourceIndex>
</template>
