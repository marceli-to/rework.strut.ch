<script setup>
import { ref, watch } from 'vue'
import { PhX } from '@phosphor-icons/vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: null },
	size: { type: String, default: 'sm' }, // sm, md, lg
})

const emit = defineEmits(['close'])

const dialogRef = ref(null)

const sizes = {
	sm: 'max-w-360',
	md: 'max-w-480',
	lg: 'max-w-640',
}

watch(() => props.open, (val) => {
	if (val) {
		dialogRef.value?.showModal()
	} else {
		dialogRef.value?.close()
	}
})

function onClose() {
	document.activeElement?.blur()
	emit('close')
}
</script>

<template>
	<dialog
		ref="dialogRef"
		class="p-0 m-auto bg-white dark:bg-warm-900 rounded-2xl backdrop:bg-black/30 w-full"
		:class="sizes[size]"
		@close="onClose"
		@click.self="onClose"
	>
		<div class="p-24">
			<!-- Header -->
			<div v-if="title || $slots.header" class="flex items-center justify-between mb-20">
				<slot name="header">
					<h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">{{ title }}</h2>
				</slot>
				<button
					type="button"
					class="text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer"
					@click="onClose"
				>
					<PhX :size="16" weight="light" />
				</button>
			</div>

			<!-- Body -->
			<div>
				<slot />
			</div>

			<!-- Footer -->
			<div v-if="$slots.footer" class="mt-24">
				<slot name="footer" />
			</div>
		</div>
	</dialog>
</template>
