<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAtelierStore } from '@/stores/atelier'
import { PhPencil, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useAtelierStore()

const pageTitles = { profile: 'Profil', team: 'Team', jobs: 'Jobs' }

const columns = [
	{ key: 'name', label: 'Seite', primary: true },
	{ key: 'actions', label: '', class: 'w-140', align: 'right' },
]

onMounted(() => {
	store.fetchPages()
})
</script>

<template>
	<div>
		<PageHeader title="Atelier" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.pages.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Keine Seiten vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.pages">
			<template #cell-name="{ row }">
				{{ pageTitles[row.slug] || row.slug }}
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="rounded transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						:class="row.publish ? 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100' : 'text-gray-300 dark:text-warm-700 hover:text-gray-600 dark:hover:text-warm-500'"
						:title="row.publish ? 'Veröffentlicht – klicken zum Verstecken' : 'Versteckt – klicken zum Veröffentlichen'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="rounded text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
						@click="router.push({ name: 'atelier.edit', params: { id: row.uuid } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
