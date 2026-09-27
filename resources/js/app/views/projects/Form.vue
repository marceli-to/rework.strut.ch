<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projects'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import topicsApi from '@/api/topics'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import SidebarLayout from '@/components/ui/form/SidebarLayout.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import AppDialog from '@/components/ui/dialog/AppDialog.vue'

const route = useRoute()
const router = useRouter()
const store = useProjectStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)
const topicOptions = ref([])
const showTopicDialog = ref(false)
const newTopicTitle = ref('')
const newTopicError = ref('')

const form = ref({
	title: '',
	location: '',
	slug: '',
	subtitle: '',
	year: null,
	description: '',
	info: '',
	meta_description: '',
	publish: false,
	feature: false,
	detail: false,
	topic_id: null,
})

function slugify(value) {
	return String(value ?? '')
		.toLowerCase()
		.replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
		.replace(/œ/g, 'oe').replace(/æ/g, 'ae')
		.normalize('NFD').replace(/\p{M}/gu, '')
		.replace(/[^a-z0-9]+/g, '-')
		.replace(/^-+|-+$/g, '')
}

function regenerateSlug() {
	form.value.slug = slugify([form.value.title, form.value.location, form.value.year].filter(Boolean).join(' '))
	delete store.errors.slug
}

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	mediaStore.setItems([])

	// Load topics for select
	try {
		const { data } = await topicsApi.index()
		topicOptions.value = data.data.map(t => ({ value: t.uuid, label: t.title }))
	} catch {}

	if (isEdit.value) {
		await store.fetchProject(route.params.id)
		if (store.current) {
			const p = store.current
			form.value = {
				title: p.title || '',
				location: p.location || '',
				slug: p.slug || '',
				subtitle: p.subtitle || '',
				year: p.year || null,
				description: p.description || '',
				info: p.info || '',
				meta_description: p.meta_description || '',
				publish: p.publish,
				feature: p.feature,
				detail: p.detail,
				topic_id: p.topic?.uuid || null,
			}
			mediaStore.setItems(p.media || [])
		}
	}

	setOriginal()
})

async function handleSubmit() {
	const tempMedia = mediaStore.tempItems.map(item => ({
		uuid: item.uuid,
		file: item.file,
		original_name: item.original_name,
		mime_type: item.mime_type,
		size: item.size,
		width: item.width,
		height: item.height,
		alt: item.alt || null,
		caption: item.caption || null,
		crop: item.crop || null,
		variant: item.variant || 'desktop',
	}))

	const success = await store.saveProject(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Projekt aktualisiert' : 'Projekt erstellt')
		bypass()
		router.push({ name: 'projects.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}

async function handleCreateTopic() {
	newTopicError.value = ''
	try {
		const { data } = await topicsApi.store({ title: newTopicTitle.value.trim(), publish: true })
		const created = data.data
		topicOptions.value.push({ value: created.uuid, label: created.title })
		form.value.topic_id = created.uuid
		showTopicDialog.value = false
		newTopicTitle.value = ''
		toast.success('Thema erstellt')
	} catch (error) {
		if (error.response?.status === 422) {
			newTopicError.value = Object.values(error.response.data.errors).flat()[0]
		} else {
			toast.error('Fehler beim Erstellen')
		}
	}
}

function onUploaded(media) { mediaStore.addItem(media) }
function onEditMedia(media) { editingMedia.value = media }
async function onSaveMedia({ uuid, data }) {
	const success = await mediaStore.updateItem(uuid, data)
	if (success) editingMedia.value = null
}
async function onDeleteMedia(media) { await mediaStore.deleteItem(media.uuid) }
function onReorderMedia(items) { mediaStore.reorder(items) }
function onSetTeaser(media) { mediaStore.setTeaser(media.uuid) }
function onSetOg(media) { mediaStore.setOg(media.uuid) }
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Projekt bearbeiten' : 'Neues Projekt'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else @submit.prevent="handleSubmit">
			<SidebarLayout>
				<div class="flex flex-col gap-24">
					<FormGroup>
						<FormLabel for="title" :error="store.errors.title">Titel *</FormLabel>
						<FormInput id="title" v-model="form.title" :hasError="!!store.errors.title" @focus="delete store.errors.title" />
					</FormGroup>

					<FormGroup>
						<FormLabel for="subtitle" :error="store.errors.subtitle">Untertitel</FormLabel>
						<FormInput id="subtitle" v-model="form.subtitle" :hasError="!!store.errors.subtitle" @focus="delete store.errors.subtitle" />
					</FormGroup>

					<div class="grid grid-cols-2 gap-24">
						<FormGroup>
							<FormLabel for="location" :error="store.errors.location">Ort *</FormLabel>
							<FormInput id="location" v-model="form.location" :hasError="!!store.errors.location" @focus="delete store.errors.location" />
						</FormGroup>
						<FormGroup>
							<FormLabel for="year" :error="store.errors.year">Jahr *</FormLabel>
							<FormInput id="year" v-model="form.year" type="number" :hasError="!!store.errors.year" @focus="delete store.errors.year" />
						</FormGroup>
					</div>

					<FormGroup>
						<FormLabel :error="store.errors.description">Beschreibung</FormLabel>
						<div class="mt-8">
							<Editor v-model="form.description" :hasError="!!store.errors.description" @focus="delete store.errors.description" />
						</div>
					</FormGroup>

					<FormGroup>
						<FormLabel :error="store.errors.info">Info</FormLabel>
						<div class="mt-8">
							<Editor v-model="form.info" :hasError="!!store.errors.info" @focus="delete store.errors.info" />
						</div>
					</FormGroup>

					<FormGroup>
						<FormLabel>Medien</FormLabel>
						<div class="mt-8 flex flex-col gap-16">
							<MediaUploader :compact="mediaStore.items.length > 0" @uploaded="onUploaded" />
							<MediaGrid
								v-if="mediaStore.items.length"
								:items="mediaStore.items"
								sidebar
								:hasOg="true"
								:hasTeaser="true"
								@edit="onEditMedia"
								@delete="onDeleteMedia"
								@reorder="onReorderMedia"
								@teaser="onSetTeaser"
								@og="onSetOg"
							/>
						</div>
					</FormGroup>

					<FormActions
						:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
						cancelLabel="Abbrechen"
						@cancel="router.push({ name: 'projects.index' })"
					/>
				</div>

				<template #sidebar>
					<div class="flex flex-col gap-24">
						<div class="flex flex-col gap-12">
							<FormGroup>
								<FormCheckbox v-model="form.publish">Veröffentlichen</FormCheckbox>
							</FormGroup>
							<FormGroup>
								<FormCheckbox v-model="form.feature">Auswahl</FormCheckbox>
							</FormGroup>
							<FormGroup>
								<FormCheckbox v-model="form.detail">Detailseite</FormCheckbox>
							</FormGroup>
						</div>
						<FormGroup>
							<FormLabel for="topic_id" :error="store.errors.topic_id">Thema</FormLabel>
							<FormSelect id="topic_id" v-model="form.topic_id" :options="topicOptions" :hasError="!!store.errors.topic_id" @focus="delete store.errors.topic_id" />
							<button type="button" class="block ml-auto text-xs text-gray-500 dark:text-warm-400 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer mt-6" @click="showTopicDialog = true">+ Hinzufügen</button>
						</FormGroup>
						<FormGroup>
							<FormLabel for="slug" :error="store.errors.slug">Slug</FormLabel>
							<FormInput id="slug" class="disabled:bg-gray-100 dark:disabled:bg-gray-800" v-model="form.slug" :hasError="!!store.errors.slug" @focus="delete store.errors.slug" disabled />
							<button type="button" class="block ml-auto text-xs text-gray-500 dark:text-warm-400 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer mt-6" @click="regenerateSlug">Regenerieren</button>
						</FormGroup>
						<FormGroup>
							<FormLabel for="meta_description" :error="store.errors.meta_description">Meta Description</FormLabel>
							<FormTextarea class="field-sizing-content" rows="4" id="meta_description" v-model="form.meta_description" :hasError="!!store.errors.meta_description" @focus="delete store.errors.meta_description" />
              <span class="block text-xs text-gray-500 dark:text-warm-400 mt-6">
                Nur für Projekte mit Detailseite, max. 160 Zeichen
              </span>
						</FormGroup>
					</div>
				</template>
			</SidebarLayout>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>

		<AppDialog 
      :open="showTopicDialog" 
      title="Neues Thema" 
      size="sm" @close="showTopicDialog = false; newTopicTitle = ''; newTopicError = ''">
			<form @submit.prevent="handleCreateTopic">
				<FormGroup>
					<FormLabel for="new_topic_title" :error="newTopicError">Titel *</FormLabel>
					<FormInput id="new_topic_title" v-model="newTopicTitle" :hasError="!!newTopicError" @focus="newTopicError = ''" />
				</FormGroup>
			</form>
			<template #footer>
				<div class="flex justify-end gap-8">
					<FormButton variant="secondary" @click="showTopicDialog = false; newTopicTitle = ''; newTopicError = ''">Abbrechen</FormButton>
					<FormButton @click="handleCreateTopic">Erstellen</FormButton>
				</div>
			</template>
		</AppDialog>
	</div>
</template>
