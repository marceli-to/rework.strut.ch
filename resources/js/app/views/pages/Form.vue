<script setup>
import { computed } from 'vue'
import { usePageStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const store = usePageStore()

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(store, {
	title: '', text: '', meta_description: '', publish: true,
}, { indexRoute: { name: 'pages.index' }, labels: { updated: 'Seite aktualisiert' } })

// Pages whose content is text + images (the others only carry SEO data)
const hasContent = computed(() => ['about', 'jobs', 'contact', 'imprint'].includes(store.current?.key))
</script>

<template>
	<ResourceForm :title="store.current?.title || 'Seite'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
		<template v-if="hasContent">
			<FormField name="text" label="Text" type="editor" v-model="form.text" :errors="errors" />
			<MediaField v-if="['about', 'jobs'].includes(store.current?.key)" label="Bilder" />
		</template>
		<p v-if="store.current?.key === 'home'" class="text-sm text-gray-500 dark:text-warm-400">
			Die Inhalte der Startseite werden unter
			<router-link :to="{ name: 'home' }" class="underline underline-offset-4">Startseite</router-link> bearbeitet.
		</p>
		<template #sidebar>
			<FormField name="meta_description" label="Meta Description" type="textarea" rows="4" hint="max. 160 Zeichen" v-model="form.meta_description" :errors="errors" />
			<MediaField label="Social-Media-Bild (OG)" collection="og" :maxFiles="1" />
		</template>
	</ResourceForm>
</template>
