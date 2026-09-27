<script setup>
import { useTeamStore } from '@/stores/resources'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'

const { form, isEdit, submit, cancel, errors, loading } = useResourceForm(useTeamStore(), {
	firstname: '', lastname: '', role: '', position: '', phone: '', email: '', cv: '', publish: false,
}, { indexRoute: { name: 'team.index' }, labels: { created: 'Mitglied erstellt', updated: 'Mitglied aktualisiert' } })
</script>

<template>
	<ResourceForm :title="isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied'" :isEdit="isEdit" :loading="loading" @submit="submit" @cancel="cancel">
		<div class="grid grid-cols-2 gap-24">
			<FormField name="firstname" label="Vorname" v-model="form.firstname" :errors="errors" required />
			<FormField name="lastname" label="Nachname" v-model="form.lastname" :errors="errors" required />
			<FormField name="role" label="Funktion" placeholder="z.B. Architekt FH SIA" v-model="form.role" :errors="errors" />
			<FormField name="position" label="Position" placeholder="z.B. Partner" v-model="form.position" :errors="errors" />
			<FormField name="phone" label="Telefon" v-model="form.phone" :errors="errors" />
			<FormField name="email" label="E-Mail" type="email" v-model="form.email" :errors="errors" />
		</div>
		<FormField name="cv" label="Lebenslauf" type="editor" v-model="form.cv" :errors="errors" />
		<MediaField label="Portrait" :maxFiles="1" />
		<template #sidebar>
			<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
		</template>
	</ResourceForm>
</template>
