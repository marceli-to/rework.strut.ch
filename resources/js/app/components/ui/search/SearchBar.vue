<script setup>
import { PhMagnifyingGlass, PhX } from '@phosphor-icons/vue'

/**
 * Search field floating at the bottom of the content area (like the legacy project list).
 * Escape clears it.
 */
defineProps({
	placeholder: { type: String, default: 'Suchen' },
})

const model = defineModel({ type: String, default: '' })
</script>

<template>
	<div class="fixed bottom-32 left-256 right-16 z-20 flex justify-center px-24 pointer-events-none">
		<div class="relative w-full max-w-400 pointer-events-auto">
			<PhMagnifyingGlass :size="16" weight="light" class="absolute left-16 top-1/2 -translate-y-1/2 text-gray-400 dark:text-warm-500" />
			<input
				v-model="model"
				type="search"
				:placeholder="placeholder"
				:aria-label="placeholder"
				class="block w-full pl-40 pr-40 py-12 rounded-full border border-gray-200 dark:border-warm-700 bg-white dark:bg-warm-800 text-sm dark:text-warm-100 placeholder:text-gray-400 dark:placeholder:text-warm-600 shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-warm-700 [&::-webkit-search-cancel-button]:hidden"
				@keydown.esc="model = ''"
			/>
			<button
				v-if="model"
				type="button"
				class="absolute right-12 top-1/2 -translate-y-1/2 p-4 rounded-full text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 cursor-pointer"
				title="Suche zurücksetzen"
				@click="model = ''"
			>
				<PhX :size="14" />
			</button>
		</div>
	</div>
</template>
