<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTeamStore } from '@/stores/team'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import SidebarLayout from '@/components/ui/form/SidebarLayout.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useTeamStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	firstname: '',
	name: '',
	title: '',
	email: '',
	cv: '',
	former: false,
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchMember(route.params.id)
		if (store.current) {
			const m = store.current
			form.value = {
				firstname: m.firstname || '',
				name: m.name || '',
				title: m.title || '',
				email: m.email || '',
				cv: m.cv || '',
				former: m.former || false,
			}
		}
	}

	setOriginal()
})

async function handleSubmit() {
	const success = await store.saveMember(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Mitglied aktualisiert' : 'Mitglied erstellt')
		bypass()
		router.push({ name: 'team.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else @submit.prevent="handleSubmit">
			<SidebarLayout>
				<div class="flex flex-col gap-24">
					<div class="grid grid-cols-2 gap-24">
						<FormGroup>
							<FormLabel for="firstname" :error="store.errors.firstname">Vorname *</FormLabel>
							<FormInput id="firstname" v-model="form.firstname" :hasError="!!store.errors.firstname" @focus="delete store.errors.firstname" />
						</FormGroup>
						<FormGroup>
							<FormLabel for="name" :error="store.errors.name">Name *</FormLabel>
							<FormInput id="name" v-model="form.name" :hasError="!!store.errors.name" @focus="delete store.errors.name" />
						</FormGroup>
					</div>

					<div class="grid grid-cols-2 gap-24">
						<FormGroup>
							<FormLabel for="title" :error="store.errors.title">Titel</FormLabel>
							<FormInput id="title" v-model="form.title" :hasError="!!store.errors.title" @focus="delete store.errors.title" />
						</FormGroup>
						<FormGroup>
							<FormLabel for="email" :error="store.errors.email">E-Mail</FormLabel>
							<FormInput id="email" v-model="form.email" :hasError="!!store.errors.email" @focus="delete store.errors.email" />
						</FormGroup>
					</div>

					<FormGroup>
						<FormLabel :error="store.errors.cv">Lebenslauf</FormLabel>
						<div class="mt-8">
							<Editor v-model="form.cv" :hasError="!!store.errors.cv" @focus="delete store.errors.cv" />
						</div>
					</FormGroup>

					<FormActions
						:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
						cancelLabel="Abbrechen"
						@cancel="router.push({ name: 'team.index' })"
					/>
				</div>

				<template #sidebar>
					<FormGroup>
						<FormCheckbox v-model="form.former">Ehemalige/r Mitarbeiter/in</FormCheckbox>
					</FormGroup>
				</template>
			</SidebarLayout>
		</form>
	</div>
</template>
