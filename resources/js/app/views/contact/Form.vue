<script setup>
import { ref, onMounted } from 'vue'
import { useKontaktStore } from '@/stores/kontakt'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const store = useKontaktStore()
const toast = useToast()

const form = ref({
	name: '',
	address: '',
	email: '',
	phone: '',
	maps_url: '',
	imprint: '',
})

const { setOriginal } = useUnsavedChanges(form)

onMounted(async () => {
	await store.fetchContact()
	if (store.contact) {
		const c = store.contact
		form.value = {
			name: c.name || '',
			address: c.address || '',
			email: c.email || '',
			phone: c.phone || '',
			maps_url: c.maps_url || '',
			imprint: c.imprint || '',
		}
	}

	setOriginal()
})

async function handleSubmit() {
	const success = await store.saveContact(form.value)
	if (success) {
		setOriginal()
		toast.success('Kontakt aktualisiert')
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader title="Kontakt" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else @submit.prevent="handleSubmit">
			<div class="flex flex-col gap-24">
				<FormGroup>
					<FormLabel for="name" :error="store.errors.name">Name *</FormLabel>
					<FormInput id="name" v-model="form.name" :hasError="!!store.errors.name" @focus="delete store.errors.name" />
				</FormGroup>

				<FormGroup>
					<FormLabel for="address" :error="store.errors.address">Adresse *</FormLabel>
					<FormTextarea id="address" v-model="form.address" :hasError="!!store.errors.address" @focus="delete store.errors.address" />
				</FormGroup>

				<div class="grid grid-cols-2 gap-24">
					<FormGroup>
						<FormLabel for="email" :error="store.errors.email">E-Mail *</FormLabel>
						<FormInput id="email" v-model="form.email" :hasError="!!store.errors.email" @focus="delete store.errors.email" />
					</FormGroup>
					<FormGroup>
						<FormLabel for="phone" :error="store.errors.phone">Telefon *</FormLabel>
						<FormInput id="phone" v-model="form.phone" :hasError="!!store.errors.phone" @focus="delete store.errors.phone" />
					</FormGroup>
				</div>

				<FormGroup>
					<FormLabel for="maps_url" :error="store.errors.maps_url">Google Maps URL</FormLabel>
					<FormInput id="maps_url" v-model="form.maps_url" :hasError="!!store.errors.maps_url" @focus="delete store.errors.maps_url" />
				</FormGroup>

				<FormGroup>
					<FormLabel :error="store.errors.imprint">Impressum</FormLabel>
					<div class="mt-8">
						<Editor v-model="form.imprint" :hasError="!!store.errors.imprint" :headings="true" @focus="delete store.errors.imprint" />
					</div>
				</FormGroup>

				<FormActions submitLabel="Speichern" />
			</div>
		</form>
	</div>
</template>
