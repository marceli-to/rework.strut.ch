<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useUsersStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	firstname: '',
	name: '',
	email: '',
	password: '',
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchUser(route.params.id)
		if (store.current) {
			const u = store.current
			form.value = {
				firstname: u.firstname || '',
				name: u.name || '',
				email: u.email || '',
				password: '',
			}
		}
	}

	setOriginal()
})

function generatePassword() {
	const chars = '23456789ABCDEFGHJKMNPQRSTUVWXYZ'
	const segment = () => Array.from({ length: 4 }, () => chars[Math.floor(Math.random() * chars.length)]).join('')
	form.value.password = `${segment()}-${segment()}-${segment()}`
}

async function handleSubmit() {
	const success = await store.saveUser(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Benutzer aktualisiert' : 'Benutzer erstellt')
		bypass()
		router.push({ name: 'users.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Benutzer bearbeiten' : 'Neuer Benutzer'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else @submit.prevent="handleSubmit">
			<div class="flex flex-col gap-24">
				<FormGroup>
					<FormLabel for="firstname" :error="store.errors.firstname">Vorname *</FormLabel>
					<FormInput id="firstname" v-model="form.firstname" :hasError="!!store.errors.firstname" @focus="delete store.errors.firstname" />
				</FormGroup>

				<FormGroup>
					<FormLabel for="name" :error="store.errors.name">Name *</FormLabel>
					<FormInput id="name" v-model="form.name" :hasError="!!store.errors.name" @focus="delete store.errors.name" />
				</FormGroup>

				<FormGroup>
					<FormLabel for="email" :error="store.errors.email">E-Mail *</FormLabel>
					<FormInput id="email" type="email" v-model="form.email" :hasError="!!store.errors.email" @focus="delete store.errors.email" />
				</FormGroup>

				<FormGroup>
					<FormLabel for="password" :error="store.errors.password">
						{{ isEdit ? 'Passwort (leer lassen um beizubehalten)' : 'Passwort *' }}
					</FormLabel>
					<FormInput id="password" v-model="form.password" :hasError="!!store.errors.password" @focus="delete store.errors.password" />
					<button
						type="button"
						class="mt-4 text-xs text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer"
						@click="generatePassword"
					>
						Passwort generieren
					</button>
				</FormGroup>

				<FormActions
					:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
					cancelLabel="Abbrechen"
					@cancel="router.push({ name: 'users.index' })"
				/>
			</div>
		</form>
	</div>
</template>
