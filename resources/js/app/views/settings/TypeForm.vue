<script setup>
import { useRoute } from 'vue-router'
import { useCategoryTypeStore } from '@/stores/resources'
import { useOptionsStore } from '@/stores/options'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'

const route = useRoute()
const options = useOptionsStore()

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useCategoryTypeStore(), {
	category_id: route.query.category || null, name_singular: '', name_plural: '', publish: true,
}, {
	indexRoute: (form) => form.category_id ? { name: 'categories.edit', params: { id: form.category_id } } : { name: 'categories.index' },
	labels: { created: 'Typ erstellt', updated: 'Typ aktualisiert' },
	afterLoad: () => options.load(true),
})
</script>

<template>
	<ResourceForm :title="isEdit ? 'Typ bearbeiten' : 'Neuer Typ'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<div class="grid grid-cols-2 gap-24">
			<FormField name="name_singular" label="Name (Einzahl)" placeholder="z.B. Wohnhaus" v-model="form.name_singular" :errors="errors" required />
			<FormField name="name_plural" label="Name (Mehrzahl)" placeholder="z.B. Wohnhäuser" v-model="form.name_plural" :errors="errors" required />
		</div>
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
			<FormField name="category_id" label="Kategorie" type="select" :options="options.categories" v-model="form.category_id" :errors="errors" required />
		</template>
	</ResourceForm>
</template>
