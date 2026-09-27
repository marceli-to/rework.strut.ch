<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useLandingStore } from '@/stores/landing'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import LinkPicker from '@/components/ui/form/LinkPicker.vue'

const route = useRoute()
const router = useRouter()
const store = useLandingStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)

const typeOptions = [
	{ value: 'image', label: 'Bild' },
	{ value: 'image_text', label: 'Bild + Text' },
]

const form = ref({
	type: 'image',
	text: '',
	link_type: null,
	link_url: null,
	publish: false,
})

const link = computed({
	get: () => ({ type: form.value.link_type, url: form.value.link_url }),
	set: (val) => {
		form.value.link_type = val.type
		form.value.link_url = val.url
	},
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	mediaStore.setItems([])

	if (isEdit.value) {
		await store.fetchSlide(route.params.id)
		if (store.current) {
			const s = store.current
			form.value = {
				type: s.type || 'image',
				text: s.text || '',
				link_type: s.link_type || null,
				link_url: s.link_url || null,
				publish: s.publish,
			}
			mediaStore.setItems(s.media || [])
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

	const success = await store.saveSlide(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Slide aktualisiert' : 'Slide erstellt')
		bypass()
		router.push({ name: 'landing.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
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
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Slide bearbeiten' : 'Neuer Slide'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="type" :error="store.errors.type">Typ</FormLabel>
				<FormSelect id="type" v-model="form.type" :options="typeOptions" :hasError="!!store.errors.type" @focus="delete store.errors.type" />
			</FormGroup>

			<FormGroup v-if="form.type === 'image_text'">
				<FormLabel :error="store.errors.text">Text</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.text" :link="false" :hasError="!!store.errors.text" @focus="delete store.errors.text" />
				</div>
			</FormGroup>

			<FormGroup>
				<FormLabel :error="store.errors.link_url">Link (optional)</FormLabel>
				<LinkPicker v-model="link" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Medien</FormLabel>
				<div class="mt-8 flex flex-col gap-16">
					<MediaUploader :compact="mediaStore.items.length > 0" @uploaded="onUploaded" />
					<MediaGrid
						v-if="mediaStore.items.length"
						:items="mediaStore.items"
						:hasVariant="true"
						:variantCropFormats="true"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
					/>
				</div>
			</FormGroup>

			<FormActions
				:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
				cancelLabel="Abbrechen"
				@cancel="router.push({ name: 'landing.index' })"
			/>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
