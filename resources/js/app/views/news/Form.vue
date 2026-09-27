<script setup>
import { useNewsStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useNewsStore(), {
	date_label: '', title: '', subtitle: '', text: '', link_url: '', link_label: '', publish: false,
}, { indexRoute: { name: 'news.index' }, labels: { created: 'News erstellt', updated: 'News aktualisiert' } })
</script>

<template>
	<ResourceForm :title="isEdit ? 'News bearbeiten' : 'Neue News'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
		<FormField name="subtitle" label="Untertitel" v-model="form.subtitle" :errors="errors" />
		<FormField name="text" label="Text" type="textarea" rows="4" v-model="form.text" :errors="errors" />
		<div class="grid grid-cols-2 gap-24">
			<FormField name="link_url" label="Link" type="url" placeholder="https://" v-model="form.link_url" :errors="errors" />
			<FormField name="link_label" label="Linktext" v-model="form.link_label" :errors="errors" />
		</div>
		<MediaField label="Bild" :maxFiles="1" />
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
			<FormField name="date_label" label="Datum" placeholder="z.B. August 2026" hint="Freitext, wird so angezeigt" v-model="form.date_label" :errors="errors" />
		</template>
	</ResourceForm>
</template>
