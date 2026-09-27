<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useTopicStore } from '@/stores/topics'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useTopicStore()
const toast = useToast()
const { confirm } = useConfirm()

const draggableTopics = ref([])

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(async () => {
	await store.fetchTopics()
	draggableTopics.value = [...store.topics]
})

watch(() => store.topics, (val) => {
	draggableTopics.value = [...val]
})

async function handleReorder() {
	const items = draggableTopics.value.map((topic, index) => ({
		uuid: topic.uuid,
		sort_order: index,
	}))
	await store.reorderTopics(items)
}

async function handleDelete(topic) {
	const ok = await confirm({
		title: 'Thema löschen',
		message: `"${topic.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteTopic(topic.uuid)
	toast.success('Thema gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Themen" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'topics.create' })">Neues Thema</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.topics.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Noch keine Themen vorhanden.
		</div>

		<DataTable
			v-else
			v-model="draggableTopics"
			:columns="columns"
			:rows="draggableTopics"
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
						@click="router.push({ name: 'topics.edit', params: { id: row.uuid } })"
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
