<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useTeamStore } from '@/stores/team'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useTeamStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'name', label: 'Name', primary: true },
	{ key: 'email', label: 'E-Mail' },
	{ key: 'title', label: 'Titel' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

const currentMembers = computed(() => store.members.filter(m => !m.former))
const formerMembers = computed(() => store.members.filter(m => m.former))

const draggableCurrentMembers = ref([])
const draggableFormerMembers = ref([])

onMounted(async () => {
	await store.fetchMembers()
	draggableCurrentMembers.value = [...currentMembers.value]
	draggableFormerMembers.value = [...formerMembers.value]
})

watch(currentMembers, (val) => {
	draggableCurrentMembers.value = [...val]
})

watch(formerMembers, (val) => {
	draggableFormerMembers.value = [...val]
})

async function handleReorderCurrent() {
	const items = draggableCurrentMembers.value.map((member, index) => ({
		uuid: member.uuid,
		sort_order: index,
	}))
	await store.reorderMembers(items)
}

async function handleReorderFormer() {
	const items = draggableFormerMembers.value.map((member, index) => ({
		uuid: member.uuid,
		sort_order: index,
	}))
	await store.reorderMembers(items)
}

async function handleDelete(member) {
	const ok = await confirm({
		title: 'Mitglied löschen',
		message: `"${member.firstname} ${member.name}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteMember(member.uuid)
	toast.success('Mitglied gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Team" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'team.create' })">Neues Mitglied</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<template v-else>
			<div class="mb-32">
				<h2 class="text-sm font-medium text-gray-500 dark:text-warm-400 mb-12">Aktiv</h2>
				<div v-if="draggableCurrentMembers.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
					Keine aktiven Mitglieder vorhanden.
				</div>
				<DataTable
					v-else
					v-model="draggableCurrentMembers"
					:columns="columns"
					:rows="draggableCurrentMembers"
					:draggable-rows="true"
					@update:model-value="handleReorderCurrent"
				>
					<template #cell-name="{ row }">
						{{ row.firstname }} {{ row.name }}
					</template>
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
								@click="router.push({ name: 'team.edit', params: { id: row.uuid } })"
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

			<div>
				<h2 class="text-sm font-medium text-gray-500 dark:text-warm-400 mb-12">Ehemalige</h2>
				<div v-if="draggableFormerMembers.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
					Keine ehemaligen Mitglieder vorhanden.
				</div>
				<DataTable
					v-else
					v-model="draggableFormerMembers"
					:columns="columns"
					:rows="draggableFormerMembers"
					:draggable-rows="true"
					@update:model-value="handleReorderFormer"
				>
					<template #cell-name="{ row }">
						{{ row.firstname }} {{ row.name }}
					</template>
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
								@click="router.push({ name: 'team.edit', params: { id: row.uuid } })"
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
	</div>
</template>
