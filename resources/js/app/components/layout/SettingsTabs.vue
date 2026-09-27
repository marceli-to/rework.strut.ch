<script setup>
import { useRoute } from 'vue-router'

// Tab bar shared by the settings screens (sidebar entry "Einstellungen")
const route = useRoute()

const tabs = [
	{ label: 'Kategorien', to: '/dashboard/categories', match: ['/dashboard/categories', '/dashboard/types'] },
	{ label: 'SEO', to: '/dashboard/seo', match: ['/dashboard/seo'] },
	{ label: 'Benutzer', to: '/dashboard/users', match: ['/dashboard/users'] },
]

const active = (tab) => tab.match.some(path => route.path.startsWith(path))
</script>

<template>
	<nav class="flex gap-32 mb-32">
		<router-link
			v-for="tab in tabs"
			:key="tab.to"
			:to="tab.to"
			class="pb-4 text-xs font-medium uppercase tracking-[0.08em] transition-colors"
			:class="active(tab)
				? 'text-gray-900 dark:text-warm-100 border-b border-neutral-900 dark:border-warm-200'
				: 'text-gray-400 dark:text-warm-500 hover:text-gray-600 dark:hover:text-warm-300'"
		>
			{{ tab.label }}
		</router-link>
	</nav>
</template>
