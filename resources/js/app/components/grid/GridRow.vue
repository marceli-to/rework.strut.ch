<script setup>
import { computed } from 'vue'
import { PhDotsSixVertical, PhTrash, PhEye, PhEyeSlash, PhCaretDown, PhNewspaper, PhPencilSimple } from '@phosphor-icons/vue'
import GridCell from '@/components/grid/GridCell.vue'

/**
 * Renders any layout from its spec (columns → stacked cells), so no
 * per-layout template exists anywhere.
 */
const props = defineProps({
	row: { type: Object, required: true },
	layout: { type: Object, required: true },
	editable: { type: Boolean, default: false }, // area offers more than one layout
	dragItem: { type: Object, default: null },
	collapsed: { type: Boolean, default: false },
})

const emit = defineEmits(['add', 'remove', 'dragstart', 'drop', 'layout', 'toggle', 'delete', 'collapse', 'edit'])

const itemAt = (position) => props.row.items.find(i => i.position === position) ?? null
const isSlideshow = computed(() => props.layout.slots === null)
const nextPosition = computed(() => props.row.items.reduce((max, i) => Math.max(max, i.position + 1), 0))
const gridColumns = computed(() => props.layout.columns.map(c => `${c.fr}fr`).join(' '))
const dragging = computed(() => !!props.dragItem)
</script>

<template>
	<div class="rounded-md border border-gray-200 dark:border-warm-700 bg-white dark:bg-warm-900" :class="row.publish ? '' : 'opacity-50'">
		<div class="flex items-center gap-12 h-52 px-12 border-b text-gray-400 dark:text-warm-500" :class="collapsed ? 'border-transparent' : 'border-gray-100 dark:border-warm-800'">
			<span class="row-handle cursor-grab active:cursor-grabbing" title="Zeile verschieben"><PhDotsSixVertical :size="16" /></span>
			<!-- collapsed: small previews keep the row recognisable while sorting -->
			<span v-if="collapsed" class="flex items-center gap-4 overflow-hidden">
				<template v-for="item in row.items" :key="item.uuid">
					<span v-if="item.type === 'news'" class="size-24 flex items-center justify-center rounded-sm border border-gray-200 dark:border-warm-700" :title="item.news.title"><PhNewspaper :size="12" /></span>
					<img v-else-if="item.media.thumbnail_url" :src="item.media.thumbnail_url" class="size-24 object-cover rounded-sm" alt="" />
					<span v-else class="size-24 rounded-sm bg-gray-200 dark:bg-warm-700" />
				</template>
			</span>
			<span class="flex-1" />
			<button v-if="editable" type="button" class="hover:text-gray-900 dark:hover:text-warm-100 cursor-pointer" title="Layout ändern" @click="emit('edit')">
				<PhPencilSimple :size="16" weight="light" />
			</button>
			<button type="button" class="hover:text-gray-900 dark:hover:text-warm-100 cursor-pointer" :title="row.publish ? 'Zeile ausblenden' : 'Zeile einblenden'" @click="emit('toggle')">
				<PhEye v-if="row.publish" :size="16" weight="light" />
				<PhEyeSlash v-else :size="16" weight="light" />
			</button>
			<button type="button" class="hover:text-red-600 cursor-pointer" title="Zeile löschen" @click="emit('delete')">
				<PhTrash :size="16" weight="light" />
			</button>
			<button type="button" class="hover:text-gray-900 dark:hover:text-warm-100 cursor-pointer transition-transform" :class="collapsed ? 'rotate-90' : ''" :title="collapsed ? 'Aufklappen' : 'Zuklappen'" @click="emit('collapse')">
				<PhCaretDown :size="14" />
			</button>
		</div>

		<div v-show="!collapsed" class="p-12">
			<!-- slideshow: open list -->
			<div v-if="isSlideshow" class="grid grid-cols-4 gap-12">
				<GridCell
					v-for="item in row.items"
					:key="item.uuid"
					:item="item"
					:dragging="dragging"
					@remove="emit('remove', item.position)"
					@dragstart="emit('dragstart', item, $event)"
					@drop="emit('drop', item.position)"
				/>
				<GridCell :dragging="dragging" @add="emit('add', nextPosition, false)" @drop="emit('drop', nextPosition)" />
			</div>

			<!-- layout grid -->
			<div v-else class="grid gap-12" :style="{ gridTemplateColumns: gridColumns }">
				<div v-for="(column, c) in layout.columns" :key="c" class="flex flex-col gap-12">
					<template v-for="(cell, i) in column.cells" :key="i">
						<div v-if="cell.size === 'spacer'" class="flex-1" />
						<GridCell
							v-else
							:item="itemAt(cell.position)"
							:ratio="cell.ratio"
							:acceptsNews="cell.news"
							:dragging="dragging"
							@add="emit('add', cell.position, cell.news)"
							@remove="emit('remove', cell.position)"
							@dragstart="emit('dragstart', itemAt(cell.position), $event)"
							@drop="emit('drop', cell.position)"
						/>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>
