<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useUsersStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'name', label: 'Name', primary: true },
	{ key: 'email', label: 'E-Mail' },
	{ key: 'actions', label: '', class: 'w-80', align: 'right' },
]

onMounted(async () => {
	await store.fetchUsers()
})

async function handleDelete(user) {
	const ok = await confirm({
		title: 'Benutzer löschen',
		message: `"${user.firstname} ${user.name}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteUser(user.uuid)
	toast.success('Benutzer gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Benutzer" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700" @click="router.push({ name: 'users.create' })">Neuer Benutzer</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<template v-else>
			<div v-if="store.users.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
				Keine Benutzer vorhanden.
			</div>
			<DataTable
				v-else
				:columns="columns"
				:rows="store.users"
			>
				<template #cell-name="{ row }">
					{{ row.firstname }} {{ row.name }}
				</template>
				<template #cell-actions="{ row }">
					<div class="flex items-center justify-end gap-12">
						<button
							class="rounded text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
							@click="router.push({ name: 'users.edit', params: { id: row.uuid } })"
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
		</template>
	</div>
</template>
