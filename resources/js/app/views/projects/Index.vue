<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProjectStore } from '@/stores/resources'
import { useOptionsStore } from '@/stores/options'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const options = useOptionsStore()
const type = ref(null)
const params = computed(() => (type.value ? { type: type.value } : {}))

const columns = [
	{ key: 'full_title', label: 'Projekt', primary: true },
	{ key: 'year', label: 'Jahr' },
	{ key: 'type', label: 'Typ' },
	{ key: 'status_label', label: 'Status' },
]

// categories alternate between solid and outlined badges
const variant = (row) => (row.category_type?.category?.sort_order ?? 0) % 2 === 0 ? 'solid' : 'outline'

onMounted(() => options.load())
</script>

<template>
	<ResourceIndex
		title="Projekte"
		:store="useProjectStore()"
		:columns="columns"
		:params="params"
		:routes="{ create: 'projects.create', edit: 'projects.edit' }"
		createLabel="Neues Projekt"
		:sortable="!!type"
		:rowLabel="row => row.full_title"
	>
		<template #actions>
			<div class="w-[18rem]">
				<FormSelect v-model="type" :options="options.category_types" placeholder="Alle Typen" />
			</div>
		</template>
		<template #cell-type="{ row }">
			<Badge v-if="row.category_type" :variant="variant(row)">{{ row.category_type.name_singular }}</Badge>
		</template>
	</ResourceIndex>
</template>
