<script setup>
import { useSeoStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const store = useSeoStore()

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(store, {
	meta_description: '',
}, { indexRoute: { name: 'seo.index' }, labels: { updated: 'SEO gespeichert' } })
</script>

<template>
	<ResourceForm :title="store.current?.title ? `SEO – ${store.current.title}` : 'SEO'" :isEdit="isEdit" :loading="loading" :sidebar="false" @submit="submit" @cancel="cancel">
		<div class="flex flex-col gap-24 max-w-[48rem]">
			<FormField name="meta_description" label="Meta Description" type="textarea" rows="4" hint="max. 160 Zeichen" v-model="form.meta_description" :errors="errors" />
			<MediaField label="Social-Media-Bild (OG)" collection="og" profile="og" :maxFiles="1" />
		</div>
	</ResourceForm>
</template>
