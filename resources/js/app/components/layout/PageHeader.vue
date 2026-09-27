<script setup>
defineProps({
	title: { type: String, required: true },
})
</script>

<template>
	<div class="flex items-center mb-40">
		<h1 class="text-lg font-medium text-gray-900 dark:text-warm-100">{{ title }}</h1>
	</div>
</template>
