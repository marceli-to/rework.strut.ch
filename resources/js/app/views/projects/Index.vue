<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projects'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash, PhStar } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useProjectStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'title', label: 'Titel', primary: true, sortable: true },
	{ key: 'location', label: 'Ort', sortable: true },
	{ key: 'year', label: 'Jahr', class: 'w-80', sortable: true },
	{ key: 'actions', label: '', class: 'w-140', align: 'right' },
]

onMounted(() => {
	store.fetchProjects()
})

async function handleDelete(project) {
	const ok = await confirm({
		title: 'Projekt löschen',
		message: `"${project.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteProject(project.uuid)
	toast.success('Projekt gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Projekte" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'projects.create' })">Neues Projekt</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.projects.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Noch keine Projekte vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.projects">
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="rounded transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						:class="row.feature ? 'text-amber-300 hover:text-amber-500' : 'text-gray-300 dark:text-warm-700 hover:text-amber-300'"
						:title="row.feature ? 'Auswahl – klicken zum Entfernen' : 'Nicht in Auswahl – klicken zum Hinzufügen'"
						@click="store.toggleFeature(row.uuid)"
					>
						<PhStar :size="16" :weight="row.feature ? 'fill' : 'light'" />
					</button>
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
						@click="router.push({ name: 'projects.edit', params: { id: row.uuid } })"
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
