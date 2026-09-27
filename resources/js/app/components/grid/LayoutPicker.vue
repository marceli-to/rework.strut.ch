<script setup>
import Drawer from '@/components/ui/drawer/Drawer.vue'
import LayoutIcon from '@/components/grid/LayoutIcon.vue'

/**
 * Drawer to choose the layout of a new grid row.
 */
defineProps({
	open: { type: Boolean, default: false },
	layouts: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'select'])
</script>

<template>
	<Drawer :open="open" title="Neue Zeile" size="sm" @close="emit('close')">
		<div class="px-24 py-16 grid grid-cols-2 gap-12">
			<button
				v-for="layout in layouts"
				:key="layout.key"
				type="button"
				class="flex flex-col items-center gap-10 px-12 py-16 rounded-md border border-gray-200 dark:border-warm-700 text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 hover:border-gray-400 cursor-pointer"
				@click="emit('select', layout.key)"
			>
				<LayoutIcon :layout="layout" :width="72" />
				<span class="text-xs text-center">{{ layout.label }}</span>
			</button>
		</div>
	</Drawer>
</template>
