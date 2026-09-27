<script setup>
import { PhX, PhCheck, PhWarning } from '@phosphor-icons/vue'

defineProps({
	toast: {
		type: Object,
		required: true,
	},
})

defineEmits(['close'])

const styles = {
	success: 'bg-emerald-600 text-white',
	error: 'bg-red-500 text-white',
}

const icons = {
	success: PhCheck,
	error: PhWarning,
}
</script>

<template>
	<div
		class="flex items-center gap-10 text-xs py-12 px-16 rounded-md shadow-sm cursor-pointer"
		:class="styles[toast.type]"
		@click="$emit('close')"
	>
		<component :is="icons[toast.type]" :size="14" weight="light" class="shrink-0" />
		<span class="flex-1">{{ toast.message }}</span>
		<PhX :size="12" weight="light" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity" />
	</div>
</template>
