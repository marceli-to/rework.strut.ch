<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormField from '@/components/ui/form/FormField.vue'

// Meta descriptions of the listing pages (the content pages have theirs in "Seiten")
const toast = useToast()
const form = ref([])
const errors = ref({})
const { setOriginal } = useUnsavedChanges(form)

onMounted(async () => {
	const { data } = await api.get('/seo')
	form.value = data.data
	setOriginal()
})

async function save() {
	errors.value = {}
	try {
		const { data } = await api.put('/seo', { pages: form.value })
		form.value = data.data
		setOriginal()
		toast.success('SEO gespeichert')
	} catch (error) {
		if (error.response?.status === 422) {
			errors.value = error.response.data.errors
			toast.error('Bitte überprüfen Sie das Formular')
		}
	}
}
</script>

<template>
	<form @submit.prevent="save">
		<PageHeader title="SEO" />
		<FormActions submitLabel="Speichern" />
		<div class="flex flex-col gap-24 max-w-[48rem]">
			<FormField
				v-for="(page, index) in form"
				:key="page.key"
				:name="`pages.${index}.meta_description`"
				:label="`Meta Description – ${page.title}`"
				type="textarea"
				rows="3"
				hint="max. 160 Zeichen"
				v-model="page.meta_description"
				:errors="errors"
			/>
		</div>
	</form>
</template>
