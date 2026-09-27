<script setup>
import Drawer from '@/components/ui/drawer/Drawer.vue'
import LayoutIcon from '@/components/grid/LayoutIcon.vue'

/**
 * Drawer to choose the layout of a new grid row, or to change the layout
 * of an existing one (`current` set).
 */
defineProps({
	open: { type: Boolean, default: false },
	layouts: { type: Array, default: () => [] },
	current: { type: String, default: null },
})

const emit = defineEmits(['close', 'select'])
</script>

<template>
	<Drawer :open="open" :title="current ? 'Layout ändern' : 'Neue Zeile'" size="sm" @close="emit('close')">
		<div class="px-24 py-16 grid grid-cols-2 gap-12">
			<button
				v-for="layout in layouts"
				:key="layout.key"
				type="button"
				class="flex flex-col items-center gap-10 px-12 py-16 rounded-md border hover:text-gray-900 dark:hover:text-warm-100 hover:border-gray-400 cursor-pointer"
				:class="layout.key === current ? 'border-gray-900 dark:border-warm-100 text-gray-900 dark:text-warm-100' : 'border-gray-200 dark:border-warm-700 text-gray-400 dark:text-warm-500'"
				@click="emit('select', layout.key)"
			>
				<LayoutIcon :layout="layout" :width="72" />
				<span class="text-xs text-center">{{ layout.label }}</span>
			</button>
		</div>
	</Drawer>
</template>
