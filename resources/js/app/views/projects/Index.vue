<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProjectStore } from '@/stores/resources'
import { useOptionsStore } from '@/stores/options'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'

const options = useOptionsStore()
const type = ref(null)
const params = computed(() => (type.value ? { type: type.value } : {}))

const columns = [
	{ key: 'full_title', label: 'Projekt', primary: true },
	{ key: 'year', label: 'Jahr' },
	{ key: 'type', label: 'Typ' },
	{ key: 'status_label', label: 'Status' },
	{ key: 'has_detail', label: 'Detailseite' },
]

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
		<template #before>
			<div class="flex items-center gap-12 mb-24 max-w-[28rem]">
				<FormSelect v-model="type" :options="options.category_types" placeholder="Alle Typen" />
			</div>
			<p v-if="!type" class="text-xs text-gray-400 dark:text-warm-500 mb-16">Zum Sortieren einen Typ wählen.</p>
		</template>
		<template #cell-type="{ row }">{{ row.category_type?.name_singular }}</template>
		<template #cell-has_detail="{ row }">{{ row.has_detail ? 'Ja' : '–' }}</template>
	</ResourceIndex>
</template>
