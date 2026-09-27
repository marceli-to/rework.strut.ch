<script setup>
import { computed } from 'vue'
import { useCategoryStore, useCategoryTypeStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import ResourceIndex from '@/components/resource/ResourceIndex.vue'
import FormField from '@/components/ui/form/FormField.vue'

const store = useCategoryStore()

const { form, isEdit, uuid, submit, cancel, errors, loading } = useResourceForm(store, {
	name: '', show_types: true, publish: true,
}, { indexRoute: { name: 'categories.index' }, labels: { created: 'Kategorie erstellt', updated: 'Kategorie aktualisiert' } })

const typeColumns = [
	{ key: 'name_singular', label: 'Einzahl', primary: true },
	{ key: 'name_plural', label: 'Mehrzahl' },
]
const typeParams = computed(() => ({ category: uuid.value }))
</script>

<template>
	<div>
		<ResourceForm :title="isEdit ? 'Kategorie bearbeiten' : 'Neue Kategorie'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
			<FormField name="name" label="Name" v-model="form.name" :errors="errors" required />
			<template #sidebar>
				<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
				<FormField name="show_types" label="Typen im Menü anzeigen" type="checkbox" v-model="form.show_types" />
			</template>
		</ResourceForm>

		<div v-if="isEdit" class="mt-48">
			<ResourceIndex
				title="Typen"
				:store="useCategoryTypeStore()"
				:columns="typeColumns"
				:params="typeParams"
				:routes="{ create: 'types.create', edit: 'types.edit' }"
				:rowLabel="row => row.name_singular"
				sortable
			>
				<template #before>
					<router-link :to="{ name: 'types.create', query: { category: uuid } }" class="inline-block mb-16 text-sm underline underline-offset-4 text-gray-500 dark:text-warm-400 hover:text-gray-900 dark:hover:text-warm-100">+ Neuer Typ</router-link>
				</template>
			</ResourceIndex>
		</div>
	</div>
</template>
