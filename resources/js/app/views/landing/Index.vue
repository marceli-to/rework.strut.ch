<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useLandingStore } from '@/stores/landing'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import landingApi from '@/api/landing'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useLandingStore()
const toast = useToast()
const { confirm } = useConfirm()

const draggableSlides = ref([])

const columns = [
	{ key: 'thumbnail', label: '', class: 'w-60' },
	{ key: 'type', label: 'Typ', primary: true },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

const typeLabels = {
	image: 'Bild',
	image_text: 'Bild + Text',
}

onMounted(async () => {
	await store.fetchSlides()
	draggableSlides.value = [...store.slides]
})

watch(() => store.slides, (val) => {
	draggableSlides.value = [...val]
})

async function handleReorder() {
	const items = draggableSlides.value.map((slide, index) => ({
		uuid: slide.uuid,
		sort_order: index,
	}))
	await landingApi.reorder(items)
}

async function handleDelete(slide) {
	const ok = await confirm({
		title: 'Slide löschen',
		message: `Slide wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteSlide(slide.uuid)
	toast.success('Slide gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Startseite" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'landing.create' })">Neuer Slide</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.slides.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Noch keine Slides vorhanden.
		</div>

		<DataTable
			v-else
			v-model="draggableSlides"
			:columns="columns"
			:rows="draggableSlides"
			:draggable-rows="true"
			@update:model-value="handleReorder"
		>
			<template #cell-thumbnail="{ row }">
				<img v-if="row.media?.length" :src="row.media[0].thumbnail_url" class="w-40 h-40 object-cover" />
			</template>
			<template #cell-type="{ row }">
				{{ typeLabels[row.type] || row.type }}
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="rounded transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						:class="row.publish ? 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100' : 'text-gray-300 dark:text-warm-700 hover:text-gray-600 dark:hover:text-warm-500'"
						:title="row.publish ? 'Veröffentlicht – klicken zum Verstecken' : 'Versteckt – klicken zum Veröffentlichen'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="rounded text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						@click="router.push({ name: 'landing.edit', params: { id: row.uuid } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
					<button
						class="rounded text-gray-400 dark:text-warm-500 hover:text-red-600 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						@click="handleDelete(row)"
					>
						<PhTrash :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
