<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useEntryStore } from '@/stores/resources'
import { useOptionsStore } from '@/stores/options'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const route = useRoute()
const type = route.meta.entryType
const options = useOptionsStore()

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useEntryStore(), {
	type, project_id: null, title: '', description: '', year: new Date().getFullYear(), url: '', publish: false,
}, {
	indexRoute: { name: `${type}.index` },
	afterLoad: () => options.load(),
})

const title = computed(() => (isEdit.value ? 'Eintrag bearbeiten' : 'Neuer Eintrag') + ' – ' + route.meta.section)
</script>

<template>
	<ResourceForm :title="title" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
		<FormField name="description" label="Beschreibung" v-model="form.description" :errors="errors" />
		<FormField name="url" label="Link" type="url" placeholder="https://" v-model="form.url" :errors="errors" />
		<MediaField label="Bild" :maxFiles="1" />
		<MediaField label="Datei (PDF)" collection="files" :maxFiles="1" />
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
			<FormField name="year" label="Jahr" type="number" v-model="form.year" :errors="errors" required />
			<FormField v-if="type === 'press'" name="project_id" label="Projekt" type="select" :options="options.projects" v-model="form.project_id" :errors="errors" />
		</template>
	</ResourceForm>
</template>
