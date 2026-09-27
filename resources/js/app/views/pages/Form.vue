<script setup>
import { usePageStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const store = usePageStore()

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(store, {
	title: '', text: '', meta_description: '', publish: true,
}, { indexRoute: { name: 'pages.index' }, labels: { updated: 'Seite aktualisiert' } })
</script>

<template>
	<ResourceForm :title="store.current?.title || 'Seite'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
		<FormField name="text" label="Text" type="editor" v-model="form.text" :errors="errors" />
		<MediaField v-if="['about', 'jobs'].includes(store.current?.key)" label="Bilder" />
		<template #sidebar>
			<FormField name="meta_description" label="Meta Description" type="textarea" rows="4" hint="max. 160 Zeichen" v-model="form.meta_description" :errors="errors" />
			<MediaField label="Social-Media-Bild (OG)" collection="og" :maxFiles="1" />
		</template>
	</ResourceForm>
</template>
