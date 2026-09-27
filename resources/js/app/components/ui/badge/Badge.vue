<script setup>
// Monochrome badge: 'solid' (black) or 'outline' (white); inverted in dark mode.
defineProps({
	variant: { type: String, default: 'solid' },
})
</script>

<template>
	<span
		class="inline-flex items-center px-8 py-3 rounded-full text-xs leading-none whitespace-nowrap border"
		:class="variant === 'solid'
			? 'bg-gray-900 border-gray-900 text-white dark:bg-warm-100 dark:border-warm-100 dark:text-warm-900'
			: 'bg-white border-gray-900 text-gray-900 dark:bg-transparent dark:border-warm-100 dark:text-warm-100'"
	>
		<slot />
	</span>
</template>
