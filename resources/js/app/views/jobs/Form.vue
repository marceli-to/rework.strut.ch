<script setup>
import { useJobStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useJobStore(), {
	title: '', lead: '', info: '', publish: false,
}, { indexRoute: { name: 'jobs.index' }, labels: { created: 'Inserat erstellt', updated: 'Inserat aktualisiert' } })
</script>

<template>
	<ResourceForm :title="isEdit ? 'Inserat bearbeiten' : 'Neues Inserat'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" placeholder="z.B. offene Stelle" v-model="form.title" :errors="errors" required />
		<FormField name="lead" label="Lead" v-model="form.lead" :errors="errors" />
		<FormField name="info" label="Info" type="editor" v-model="form.info" :errors="errors" />
		<MediaField label="Inserat (PDF)" collection="files" :maxFiles="1" />
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
		</template>
	</ResourceForm>
</template>
