<script setup>
import { useBookStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useBookStore(), {
	title: '', description: '', info: '', url: '', publish: false,
}, { indexRoute: { name: 'books.index' }, labels: { created: 'Buch erstellt', updated: 'Buch aktualisiert' } })
</script>

<template>
	<ResourceForm :title="isEdit ? 'Buch bearbeiten' : 'Neues Buch'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
		<FormField name="description" label="Angaben" type="textarea" rows="5" hint="Umfang, Format, Preis – eine Angabe pro Zeile" v-model="form.description" :errors="errors" />
		<FormField name="info" label="Beschreibung" type="editor" v-model="form.info" :errors="errors" />
		<MediaField label="Cover" :maxFiles="1" />
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
			<FormField name="url" label="Bestellung" placeholder="https://… oder E-Mail" hint="Web-Adresse oder E-Mail für die Bestellung" v-model="form.url" :errors="errors" />
		</template>
	</ResourceForm>
</template>
