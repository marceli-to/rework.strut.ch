<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useJobStore } from '@/stores/jobs'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useJobStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	title: '',
	text: '',
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchJob(route.params.id)
		if (store.current) {
			form.value = {
				title: store.current.title || '',
				text: store.current.text || '',
			}
		}
	}

	setOriginal()
})

async function handleSubmit() {
	const success = await store.saveJob(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Stelle aktualisiert' : 'Stelle erstellt')
		bypass()
		router.push({ name: 'jobs.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Stelle bearbeiten' : 'Neue Stelle'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="title" :error="store.errors.title">Titel *</FormLabel>
				<FormInput id="title" v-model="form.title" :hasError="!!store.errors.title" @focus="delete store.errors.title" />
			</FormGroup>

			<FormGroup>
				<FormLabel :error="store.errors.text">Beschreibung *</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.text" :hasError="!!store.errors.text" @focus="delete store.errors.text" />
				</div>
			</FormGroup>

			<FormActions
				:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
				cancelLabel="Abbrechen"
				@cancel="router.push({ name: 'jobs.index' })"
			/>
		</form>
	</div>
</template>
