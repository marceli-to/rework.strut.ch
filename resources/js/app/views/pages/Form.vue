<script setup>
import { ref } from 'vue'
import { usePageStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'
import Tabs from '@/components/ui/tabs/Tabs.vue'
import Tab from '@/components/ui/tabs/Tab.vue'

const store = usePageStore()
const tab = ref('content')
const tabs = [
	{ key: 'content', label: 'Inhalt' },
	{ key: 'seo', label: 'SEO' },
]

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(store, {
	title: '', text: '', meta_description: '', publish: true,
}, { indexRoute: { name: 'pages.index' }, labels: { updated: 'Seite aktualisiert' } })
</script>

<template>
	<ResourceForm :title="store.current?.title || 'Seite'" :isEdit="isEdit" :loading="loading" :sidebar="false" @submit="submit" @cancel="cancel">
		<Tabs v-model="tab" :tabs="tabs">
			<Tab name="content">
				<div class="flex flex-col gap-24 max-w-[48rem]">
					<FormField name="title" label="Titel" v-model="form.title" :errors="errors" required />
					<FormField name="text" label="Text" type="editor" v-model="form.text" :errors="errors" />
					<MediaField v-if="['about', 'jobs'].includes(store.current?.key)" label="Bilder" profile="page" />
				</div>
			</Tab>
			<Tab name="seo">
				<div class="flex flex-col gap-24 max-w-[48rem]">
					<FormField name="meta_description" label="Meta Description" type="textarea" rows="4" hint="max. 160 Zeichen" v-model="form.meta_description" :errors="errors" />
					<MediaField label="Social-Media-Bild (OG)" collection="og" profile="og" :maxFiles="1" />
				</div>
			</Tab>
		</Tabs>
	</ResourceForm>
</template>
