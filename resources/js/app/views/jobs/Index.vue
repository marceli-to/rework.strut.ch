<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useJobStore } from '@/stores/jobs'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useJobStore()
const toast = useToast()
const { confirm } = useConfirm()

const draggableJobs = ref([])

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(async () => {
	await store.fetchJobs()
	draggableJobs.value = [...store.jobs]
})

watch(() => store.jobs, (val) => {
	draggableJobs.value = [...val]
})

async function handleReorder() {
	const items = draggableJobs.value.map((job, index) => ({
		uuid: job.uuid,
		sort_order: index,
	}))
	await store.reorderJobs(items)
}

async function handleDelete(job) {
	const ok = await confirm({
		title: 'Stelle löschen',
		message: `"${job.title}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteJob(job.uuid)
	toast.success('Stelle gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Jobs" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'jobs.create' })">Neue Stelle</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.jobs.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Noch keine Jobs vorhanden.
		</div>

		<DataTable
			v-else
			v-model="draggableJobs"
			:columns="columns"
			:rows="draggableJobs"
			:draggable-rows="true"
			@update:model-value="handleReorder"
		>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="rounded transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						:class="row.publish ? 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100' : 'text-gray-300 dark:text-warm-700 hover:text-gray-600 dark:hover:text-warm-500'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="rounded text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						@click="router.push({ name: 'jobs.edit', params: { id: row.uuid } })"
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
