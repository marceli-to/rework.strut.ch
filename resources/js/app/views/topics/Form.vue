<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTopicStore } from '@/stores/topics'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useTopicStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	title: '',
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchTopic(route.params.id)
		if (store.current) {
			form.value = {
				title: store.current.title || '',
			}
		}
	}

	setOriginal()
})

async function handleSubmit() {
	const success = await store.saveTopic(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Thema aktualisiert' : 'Thema erstellt')
		bypass()
		router.push({ name: 'topics.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Thema bearbeiten' : 'Neues Thema'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="title" :error="store.errors.title">Titel *</FormLabel>
				<FormInput id="title" v-model="form.title" :hasError="!!store.errors.title" @focus="delete store.errors.title" />
			</FormGroup>

			<FormActions
				:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
				cancelLabel="Abbrechen"
				@cancel="router.push({ name: 'topics.index' })"
			/>
		</form>
	</div>
</template>
