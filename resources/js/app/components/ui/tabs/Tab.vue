<script setup>
import { inject } from 'vue'

defineProps({
	name: { type: String, required: true },
})

const activeTab = inject('activeTab')
</script>

<template>
	<div v-if="activeTab === name">
		<slot />
	</div>
</template>
