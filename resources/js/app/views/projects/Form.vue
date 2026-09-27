<script setup>
import { ref, computed } from 'vue'
import { useProjectStore } from '@/stores/resources'
import { useOptionsStore } from '@/stores/options'
import { useResourceForm } from '@/composables/useResourceForm'
import ResourceForm from '@/components/resource/ResourceForm.vue'
import FormField from '@/components/ui/form/FormField.vue'
import MediaField from '@/components/media/MediaField.vue'
import GridEditor from '@/components/grid/GridEditor.vue'
import Tabs from '@/components/ui/tabs/Tabs.vue'
import Tab from '@/components/ui/tabs/Tab.vue'

const store = useProjectStore()
const options = useOptionsStore()
const tab = ref('data')

const { form, isEdit, uuid, submit, cancel, errors, loading } = useResourceForm(store, {
	category_type_id: null, title: '', name: '', location: '', year: new Date().getFullYear(), slug: '',
	description: '', info: '', status: 'executed', competition: null, has_detail: false, meta_description: '', publish: false,
}, {
	indexRoute: { name: 'projects.index' },
	labels: { created: 'Projekt erstellt', updated: 'Projekt aktualisiert' },
	afterLoad: () => options.load(),
	stay: true,
	editRoute: 'projects.edit',
})

const tabs = computed(() => [
	{ key: 'data', label: 'Daten' },
	{ key: 'images', label: 'Bilder & Videos' },
	{ key: 'files', label: 'Dokumentation' },
	...(isEdit.value ? [{ key: 'grid', label: 'Raster' }] : []),
])
</script>

<template>
	<ResourceForm :title="isEdit ? 'Projekt bearbeiten' : 'Neues Projekt'" :isEdit="isEdit" :loading="loading" :sidebar="tab !== 'grid'" @submit="submit" @cancel="cancel">
		<Tabs v-model="tab" :tabs="tabs">
			<Tab name="data">
				<div class="flex flex-col gap-24">
					<div class="grid grid-cols-2 gap-24">
						<FormField name="name" label="Name" placeholder="z.B. Sky-Frame, Produktions- und Verwaltungsgebäude" v-model="form.name" :errors="errors" required />
						<FormField name="location" label="Ort" v-model="form.location" :errors="errors" required />
					</div>
					<FormField name="title" label="Kurztitel" hint="Für Bildlegenden auf der Startseite, z.B. «Sky-Frame, Frauenfeld». Leer = Name, Ort" v-model="form.title" :errors="errors" />
					<FormField name="description" label="Beschreibung" type="editor" v-model="form.description" :errors="errors" />
					<FormField name="info" label="Info" type="editor" v-model="form.info" :errors="errors" />
				</div>
			</Tab>
			<Tab name="images">
				<MediaField label="Bilder & Videos" hasTeaser hasOg />
			</Tab>
			<Tab name="files">
				<MediaField label="Projektdokumentation (PDF)" collection="files" />
			</Tab>
			<Tab name="grid">
				<p class="text-xs text-gray-400 dark:text-warm-500 mb-16">Neu hochgeladene Bilder sind nach dem Speichern verfügbar. Änderungen am Raster werden sofort gespeichert.</p>
				<GridEditor v-if="uuid" context="project" :owner="uuid" />
			</Tab>
		</Tabs>

		<template #sidebar>
			<div class="flex flex-col gap-12">
				<FormField name="publish" label="Veröffentlichen" type="checkbox" v-model="form.publish" />
				<FormField name="has_detail" label="Detailseite" type="checkbox" v-model="form.has_detail" />
			</div>
			<FormField name="category_type_id" label="Typ" type="select" :options="options.category_types" v-model="form.category_type_id" :errors="errors" required />
			<FormField name="year" label="Jahr" type="number" v-model="form.year" :errors="errors" required />
			<FormField name="status" label="Status" type="select" :options="options.status" v-model="form.status" :errors="errors" required />
			<FormField name="competition" label="Wettbewerb" type="select" :options="options.competition" v-model="form.competition" :errors="errors" />
			<FormField name="slug" label="Slug" hint="Wird beim Erstellen generiert; bestehende URLs bleiben gültig." v-model="form.slug" :errors="errors" />
			<FormField name="meta_description" label="Meta Description" type="textarea" rows="4" hint="Leer = Anfang der Beschreibung (160 Zeichen)" v-model="form.meta_description" :errors="errors" />
		</template>
	</ResourceForm>
</template>
