<script setup>
import { useRoute } from 'vue-router'
import {
	PhBooks,
	PhBriefcase,
	PhBuildings,
	PhFiles,
	PhHouse,
	PhMicrophoneStage,
	PhNewspaper,
	PhNewspaperClipping,
	PhSignOut,
	PhTag,
	PhTrophy,
	PhUserCircle,
	PhUsers,
} from '@phosphor-icons/vue'
import StrutLogo from '@/components/layout/StrutLogo.vue'

const route = useRoute()

const navigation = [
	{
		items: [
			{ name: 'Startseite', to: '/dashboard/home', icon: PhHouse },
			{ name: 'News', to: '/dashboard/news', icon: PhNewspaper },
		],
	},
	{
		items: [
			{ name: 'Projekte', to: '/dashboard/projects', icon: PhBuildings },
		],
	},
	{
		items: [
			{ name: 'Presse', to: '/dashboard/press', icon: PhNewspaperClipping },
			{ name: 'Bücher', to: '/dashboard/books', icon: PhBooks },
		],
	},
	{
		items: [
			{ name: 'Team', to: '/dashboard/team', icon: PhUsers },
			{ name: 'Jobs', to: '/dashboard/jobs', icon: PhBriefcase },
			{ name: 'Auszeichnungen', to: '/dashboard/awards', icon: PhTrophy },
			{ name: 'Vorträge', to: '/dashboard/lectures', icon: PhMicrophoneStage },
		],
	},
	{
		items: [
			{ name: 'Seiten', to: '/dashboard/pages', icon: PhFiles },
			{ name: 'Kategorien', to: '/dashboard/categories', icon: PhTag },
			{ name: 'Benutzer', to: '/dashboard/users', icon: PhUserCircle },
		],
	},
]

function isActive(item) {
	if (item.exact) return route.path === item.to
	return route.path.startsWith(item.to)
}

function logout() {
	const token = document.querySelector('meta[name="csrf-token"]')?.content
	fetch('/logout', {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': token,
			'Content-Type': 'application/json',
		},
	}).then(() => {
		window.location.href = '/login'
	})
}
</script>

<template>
	<aside class="fixed top-0 left-0 bottom-0 w-240 bg-gray-100 dark:bg-warm-950 flex flex-col z-30">

		<!-- Brand -->
		<div class="px-24 pt-24 pb-24 text-gray-900 dark:text-warm-100">
			<StrutLogo class="w-96" />
		</div>

		<!-- Navigation -->
		<nav class="flex-1 py-16 px-16 overflow-y-auto">
			<div class="space-y-16">
				<div v-for="(group, index) in navigation" :key="index">
					<ul role="list">
						<li v-for="item in group.items" :key="item.to">
							<router-link
								:to="item.to"
								class="flex items-center gap-12 px-12 py-10 text-sm rounded-md transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
								:class="isActive(item)
									? 'text-gray-900 dark:text-warm-100'
									: 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100'"
							>
								<component :is="item.icon" :size="18" weight="light" />
								<span>{{ item.name }}</span>
							</router-link>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<!-- Logout -->
		<div class="px-12 pb-24">
			<button
				@click="logout"
				class="flex items-center gap-8 px-12 py-10 w-full text-xs rounded-md text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors duration-150 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
			>
				<PhSignOut :size="18" weight="light" />
				<span>Abmelden</span>
			</button>
		</div>

	</aside>
</template>
