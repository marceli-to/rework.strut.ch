<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

/**
 * List view for a resource store: table, publish toggle, edit, delete and
 * (optionally) drag & drop ordering. Cell slots are passed to the table.
 * Column options (besides DataTable's): limit (max. characters, full text on hover).
 */
const props = defineProps({
	title: { type: String, required: true },
	store: { type: Object, required: true },
	columns: { type: Array, required: true },
	routes: { type: Object, required: true }, // { create, edit } route names
	createLabel: { type: String, default: null },
	sortable: { type: Boolean, default: false },
	deletable: { type: Boolean, default: true },
	togglable: { type: [Boolean, Function], default: true }, // or row => boolean
	params: { type: Object, default: () => ({}) },
	rowLabel: { type: Function, default: row => row.title ?? row.name },
	groupBy: { type: Function, default: null }, // see DataTable
})

const router = useRouter()
const toast = useToast()
const { confirm } = useConfirm()
const rows = ref([])

const columns = [...props.columns, { key: 'actions', label: '', class: 'w-100', align: 'right' }]

async function load() {
	await props.store.fetchAll(props.params)
}

onMounted(load)
watch(() => props.params, load, { deep: true })
watch(() => props.store.items, items => { rows.value = [...items] }, { immediate: true })

function edit(row) {
	router.push({ name: props.routes.edit, params: { id: row.uuid } })
}

async function remove(row) {
	const ok = await confirm({
		title: 'Eintrag löschen',
		message: `"${props.rowLabel(row)}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await props.store.destroy(row.uuid)
	toast.success('Gelöscht')
}
</script>

<template>
	<div>
		<PageHeader :title="title" />
		<FormActions v-if="createLabel || $slots.actions">
			<slot name="actions" />
			<button v-if="createLabel" type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer" @click="router.push({ name: routes.create, query: $route.query })">
				{{ createLabel }}
			</button>
		</FormActions>

		<slot name="before" />

		<div v-if="store.loading && !rows.length" class="text-sm text-gray-400 dark:text-warm-500">Laden...</div>
		<div v-else-if="!rows.length" class="text-sm text-gray-400 dark:text-warm-500">Keine Einträge vorhanden.</div>
		<DataTable
			v-else
			v-model="rows"
			:columns="columns"
			:rows="rows"
			:draggable-rows="sortable"
			:group-by="groupBy"
			@update:model-value="store.reorder($event)"
		>
			<template v-for="col in props.columns" #[`cell-${col.key}`]="scope">
				<slot :name="`cell-${col.key}`" v-bind="scope">
					<button type="button" v-if="col.primary" class="text-left cursor-pointer hover:underline" @click="edit(scope.row)">{{ scope.value }}</button>
					<span v-else-if="col.limit && scope.value?.length > col.limit" :title="scope.value">{{ scope.value.slice(0, col.limit).trimEnd() }}…</span>
					<template v-else>{{ scope.value }}</template>
				</slot>
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						v-if="typeof togglable === 'function' ? togglable(row) : togglable"
						type="button"
						class="rounded transition-colors cursor-pointer"
						:class="row.publish ? 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100' : 'text-gray-300 dark:text-warm-700 hover:text-gray-600 dark:hover:text-warm-500'"
						:title="row.publish ? 'Veröffentlicht' : 'Nicht veröffentlicht'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button type="button" class="rounded text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer" title="Bearbeiten" @click="edit(row)">
						<PhPencil :size="16" weight="light" />
					</button>
					<button v-if="deletable" type="button" class="rounded text-gray-400 dark:text-warm-500 hover:text-red-600 transition-colors cursor-pointer" title="Löschen" @click="remove(row)">
						<PhTrash :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
