<script setup>
import { ref, computed, onMounted } from 'vue'
import draggable from 'vuedraggable'
import { createGridApi } from '@/api/grids'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import GridRow from '@/components/grid/GridRow.vue'
import GridPicker from '@/components/grid/GridPicker.vue'
import LayoutPicker from '@/components/grid/LayoutPicker.vue'

/**
 * The one grid editor for every grid context (project grid, homepage).
 * Everything context-specific comes from the API config (config/grids.php).
 * Changes are saved immediately.
 */
const props = defineProps({
	context: { type: String, required: true }, // 'project' | 'home'
	owner: { type: String, required: true },   // project uuid | 'home'
})

const api = createGridApi(props.context, props.owner)
const toast = useToast()
const { confirm } = useConfirm()

const config = ref(null)
const rows = ref([])
const options = ref({ media: [], news: [] })
const picker = ref(null) // { row, position, acceptsNews }
const dragItem = ref(null) // { row, position }
const collapsed = ref(new Set()) // row uuids
const layoutPicker = ref(null) // { area, row } — row set when changing a layout

const layouts = computed(() => Object.fromEntries((config.value?.layouts ?? []).map(l => [l.key, l])))
const rowsIn = (area) => rows.value.filter(r => r.area === area)
const layoutsIn = (area) => area.layouts.map(key => layouts.value[key])

async function run(request, message = null) {
	try {
		const result = await request()
		if (message) toast.success(message)
		return result
	} catch (error) {
		const errors = error.response?.data?.errors
		toast.error(errors ? Object.values(errors).flat()[0] : 'Aktion fehlgeschlagen')
		return null
	}
}

async function load() {
	const { data } = await api.show()
	config.value = data.config
	rows.value = data.rows
	// existing rows start collapsed; rows added later stay open for filling
	collapsed.value = new Set(data.rows.map(r => r.uuid))
}

async function loadOptions() {
	const { data } = await api.options()
	options.value = data
}

function replaceRow(row) {
	rows.value = rows.value.map(r => (r.uuid === row.uuid ? row : r))
}

function toggleCollapse(row) {
	const set = new Set(collapsed.value)
	set.has(row.uuid) ? set.delete(row.uuid) : set.add(row.uuid)
	collapsed.value = set
}

const allCollapsed = (area) => rowsIn(area.key).length > 0 && rowsIn(area.key).every(r => collapsed.value.has(r.uuid))

function toggleAll(area) {
	const set = new Set(collapsed.value)
	const collapse = !allCollapsed(area)
	rowsIn(area.key).forEach(r => (collapse ? set.add(r.uuid) : set.delete(r.uuid)))
	collapsed.value = set
}

// one layout (slideshow): add directly, otherwise choose in the drawer
function newRow(area) {
	const layouts = layoutsIn(area)
	layouts.length === 1 ? addRow(area, layouts[0].key) : (layoutPicker.value = { area, row: null })
}

function selectLayout(layout) {
	const { area, row } = layoutPicker.value
	layoutPicker.value = null
	row ? changeLayout(row, layout) : addRow(area, layout)
}

async function addRow(area, layout) {
	const response = await run(() => api.storeRow({ area: area.key, layout }))
	if (response) rows.value.push(response.data.data)
}

// items the new layout can't hold (same rule as UpdateRowAction)
function droppedBy(row, layout) {
	const spec = layouts.value[layout]
	if (spec.slots === null) return []
	const cells = spec.columns.flatMap(c => c.cells).filter(c => c.size !== 'spacer')
	return row.items.filter(item => {
		const cell = cells.find(c => c.position === item.position)
		return !cell || (item.type === 'news' && !cell.news)
	})
}

async function changeLayout(row, layout) {
	if (layout === row.layout) return
	const dropped = droppedBy(row, layout).length
	if (dropped) {
		const ok = await confirm({
			title: 'Layout ändern',
			message: `Das neue Layout hat keinen Platz für ${dropped === 1 ? '1 Inhalt' : `${dropped} Inhalte`} dieser Zeile. ${dropped === 1 ? 'Er wird' : 'Sie werden'} aus der Zeile entfernt.`,
			confirmLabel: 'Layout ändern',
			destructive: true,
		})
		if (!ok) return
	}
	const response = await run(() => api.updateRow(row.uuid, { layout }))
	if (response) replaceRow(response.data.data)
}

async function toggleRow(row) {
	const response = await run(() => api.updateRow(row.uuid, { publish: !row.publish }))
	if (response) replaceRow(response.data.data)
}

async function deleteRow(row) {
	const ok = await confirm({ title: 'Zeile löschen', message: 'Zeile mit allen platzierten Inhalten löschen?', confirmLabel: 'Löschen', destructive: true })
	if (!ok) return
	if (await run(() => api.destroyRow(row.uuid))) rows.value = rows.value.filter(r => r.uuid !== row.uuid)
}

async function reorder(area, list) {
	const others = rows.value.filter(r => r.area !== area.key)
	rows.value = [...others, ...list]
	await run(() => api.reorderRows(list.map((r, i) => ({ uuid: r.uuid, sort_order: i }))))
}

async function openPicker(row, position, acceptsNews) {
	picker.value = { row, position, acceptsNews }
	await loadOptions()
}

async function select(data) {
	const { row, position } = picker.value
	picker.value = null
	const response = await run(() => api.setItem(row.uuid, position, data))
	if (response) replaceRow(response.data.data)
}

async function removeItem(row, position) {
	const response = await run(() => api.destroyItem(row.uuid, position))
	if (response) replaceRow(response.data.data)
}

function startDrag(row, item, event) {
	dragItem.value = { row: row.uuid, position: item.position }
	event.dataTransfer.effectAllowed = 'move'
}

async function drop(row, position) {
	const from = dragItem.value
	dragItem.value = null
	if (!from || (from.row === row.uuid && from.position === position)) return
	const response = await run(() => api.moveItem({ from_row: from.row, from_position: from.position, to_row: row.uuid, to_position: position }))
	if (response) rows.value = response.data.rows
}

onMounted(load)
</script>

<template>
	<div v-if="config" class="flex flex-col gap-40" @dragend="dragItem = null">
		<section v-for="area in config.areas" :key="area.key">
			<div class="flex items-center gap-16 mb-24">
				<h2 v-if="config.areas.length > 1" class="text-sm font-medium text-gray-500 dark:text-warm-400">{{ area.label }}</h2>
				<span class="flex-1" />
				<button v-if="rowsIn(area.key).length > 1" type="button" class="text-xs text-gray-500 dark:text-warm-400 hover:text-gray-900 dark:hover:text-warm-100 cursor-pointer" @click="toggleAll(area)">
					{{ allCollapsed(area) ? 'Alle ausklappen' : 'Alle einklappen' }}
				</button>
				<button
					v-if="!area.max_rows || rowsIn(area.key).length < area.max_rows"
					type="button"
					class="text-sm px-12 py-6 rounded-md border border-gray-200 dark:border-warm-700 text-gray-700 dark:text-warm-300 hover:border-gray-400 cursor-pointer"
					@click="newRow(area)"
				>
					+ {{ area.max_rows === 1 ? 'Bereich anlegen' : 'Zeile hinzufügen' }}
				</button>
			</div>
			<p v-if="!rowsIn(area.key).length" class="text-sm text-gray-400 dark:text-warm-500">Noch keine Zeilen.</p>

			<draggable
				:modelValue="rowsIn(area.key)"
				item-key="uuid"
				handle=".row-handle"
				class="flex flex-col gap-16"
				ghost-class="opacity-30"
				animation="150"
				@update:modelValue="reorder(area, $event)"
			>
				<template #item="{ element: row }">
					<GridRow
						:row="row"
						:layout="layouts[row.layout]"
						:editable="area.layouts.length > 1"
						:dragItem="dragItem"
						:collapsed="collapsed.has(row.uuid)"
						@add="(position, acceptsNews) => openPicker(row, position, acceptsNews)"
						@remove="position => removeItem(row, position)"
						@dragstart="(item, event) => startDrag(row, item, event)"
						@drop="position => drop(row, position)"
						@edit="layoutPicker = { area, row }"
						@toggle="toggleRow(row)"
						@delete="deleteRow(row)"
						@collapse="toggleCollapse(row)"
					/>
				</template>
			</draggable>

		</section>

		<LayoutPicker
			:open="!!layoutPicker"
			:layouts="layoutPicker ? layoutsIn(layoutPicker.area) : []"
			:current="layoutPicker?.row?.layout ?? null"
			@close="layoutPicker = null"
			@select="selectLayout"
		/>

		<GridPicker
			:open="!!picker"
			:media="options.media"
			:news="options.news"
			:acceptsNews="picker?.acceptsNews ?? false"
			@close="picker = null"
			@select="select"
		/>
	</div>
	<div v-else class="text-sm text-gray-400 dark:text-warm-500">Laden...</div>
</template>
