<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAtelierStore } from '@/stores/atelier'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
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
const store = useAtelierStore()
const mediaStore = useMediaStore()
const toast = useToast()

const editingMedia = ref(null)

const pageTitles = { profile: 'Profil', team: 'Team', jobs: 'Jobs' }
const pageTitle = computed(() => {
	const slug = store.current?.slug
	return slug ? `${pageTitles[slug]} bearbeiten` : 'Bearbeiten'
})

const isProfile = computed(() => store.current?.slug === 'profile')

const form = ref({
	publish: false,
})

const { setOriginal, bypass } = useUnsavedChanges(form)

onMounted(async () => {
	mediaStore.setItems([])
	await store.fetchPage(route.params.id)
	if (store.current) {
		const p = store.current
		form.value = {
			publish: p.publish,
			...(p.slug === 'profile' ? { title: p.title || '', text: p.text || '' } : {}),
		}
		mediaStore.setItems(p.media || [])
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

	const success = await store.savePage(
		form.value,
		route.params.id,
		tempMedia
	)

	if (success) {
		toast.success('Seite aktualisiert')
		bypass()
		router.push({ name: 'atelier.index' })
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
		<PageHeader :title="pageTitle" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else-if="store.current" @submit.prevent="handleSubmit">
			<!-- Profil: sidebar layout (has title + editor + media to fill main column) -->
			<SidebarLayout v-if="store.current.slug === 'profile'">
				<div class="flex flex-col gap-24">
					<FormGroup>
						<FormLabel for="title" :error="store.errors.title">Titel *</FormLabel>
						<FormInput id="title" v-model="form.title" :hasError="!!store.errors.title" @focus="delete store.errors.title" />
					</FormGroup>

					<FormGroup>
						<FormLabel :error="store.errors.text">Text *</FormLabel>
						<div class="mt-8">
							<Editor v-model="form.text" :hasError="!!store.errors.text" @focus="delete store.errors.text" />
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
								@edit="onEditMedia"
								@delete="onDeleteMedia"
								@reorder="onReorderMedia"
							/>
						</div>
					</FormGroup>

					<FormActions
						submitLabel="Aktualisieren"
						cancelLabel="Abbrechen"
						@cancel="router.push({ name: 'atelier.index' })"
					/>
				</div>

				<template #sidebar>
					<FormGroup>
						<FormCheckbox v-model="form.publish">Veröffentlichen</FormCheckbox>
					</FormGroup>
				</template>
			</SidebarLayout>

			<!-- Team / Jobs: single column (only media + meta desc, no sidebar needed) -->
			<div v-else class="flex flex-col gap-24">
				<FormGroup>
					<FormLabel>Medien</FormLabel>
					<div class="mt-8 flex flex-col gap-16">
						<MediaUploader :compact="mediaStore.items.length > 0" @uploaded="onUploaded" />
						<MediaGrid
							v-if="mediaStore.items.length"
							:items="mediaStore.items"
							@edit="onEditMedia"
							@delete="onDeleteMedia"
							@reorder="onReorderMedia"
						/>
					</div>
				</FormGroup>

				<FormActions
					submitLabel="Aktualisieren"
					cancelLabel="Abbrechen"
					@cancel="router.push({ name: 'atelier.index' })"
				/>
			</div>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
