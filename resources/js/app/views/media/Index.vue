<script setup>
import { ref, onMounted, computed } from 'vue'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import mediaApi from '@/api/media'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import { PhMagnifyingGlass } from '@phosphor-icons/vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'

const toast = useToast()
const { confirm } = useConfirm()

const items = ref([])
const loading = ref(true)
const editingMedia = ref(null)
const search = ref('')

const filteredItems = computed(() => {
	if (!search.value.trim()) return items.value
	const q = search.value.toLowerCase()
	return items.value.filter(item =>
		(item.original_name || '').toLowerCase().includes(q) ||
		(item.alt || '').toLowerCase().includes(q) ||
		(item.caption || '').toLowerCase().includes(q)
	)
})

onMounted(async () => {
	await fetchMedia()
})

async function fetchMedia() {
	loading.value = true
	try {
		const { data } = await mediaApi.index()
		items.value = data.data
	} finally {
		loading.value = false
	}
}

function onUploaded(mediaData) {
	items.value.unshift(mediaData)
	toast.success('Bild hochgeladen')
}

function openEdit(media) {
	editingMedia.value = media
}

async function handleSave({ uuid, data }) {
	try {
		const { data: response } = await mediaApi.update(uuid, data)
		const index = items.value.findIndex(i => i.uuid === uuid)
		if (index !== -1) {
			items.value[index] = response.data
		}
		editingMedia.value = null
		toast.success('Gespeichert')
	} catch {
		toast.error('Fehler beim Speichern')
	}
}

async function handleDelete(media) {
	const ok = await confirm({
		title: 'Bild löschen',
		message: `"${media.original_name}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	try {
		await mediaApi.destroy(media.uuid)
		items.value = items.value.filter(i => i.uuid !== media.uuid)
		toast.success('Bild gelöscht')
	} catch {
		toast.error('Fehler beim Löschen')
	}
}

</script>

<template>
	<div>
		<PageHeader title="Media" />

		<!-- Upload -->
		<div class="mb-24">
			<MediaUploader compact @uploaded="onUploaded" />
		</div>

		<!-- Search -->
		<div class="relative mb-24" v-if="items.length > 0">
			<PhMagnifyingGlass :size="14" class="absolute left-12 top-1/2 -translate-y-1/2 text-gray-400 dark:text-warm-500" />
			<input
				v-model="search"
				type="text"
				placeholder="Suchen..."
				class="w-full border border-neutral-200 dark:border-warm-700 pl-32 pr-12 py-10 text-sm text-gray-900 dark:text-warm-100 dark:placeholder:text-warm-600 focus:outline-none focus:border-neutral-400 dark:focus:border-warm-600 bg-white dark:bg-warm-800"
			/>
		</div>

		<!-- Loading -->
		<div v-if="loading" class="text-sm text-gray-400">
			Laden...
		</div>

		<!-- Empty -->
		<div v-else-if="items.length === 0" class="text-sm text-gray-400">
			Noch keine Medien vorhanden.
		</div>

		<!-- Grid -->
		<MediaGrid
			v-else-if="filteredItems.length > 0"
			:items="filteredItems"
			@edit="openEdit"
			@delete="handleDelete"
		/>


		<!-- No search results -->
		<div v-else class="text-sm text-gray-400">
			Keine Ergebnisse für "{{ search }}".
		</div>

		<!-- Edit Modal -->
		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="handleSave"
		/>
	</div>
</template>
