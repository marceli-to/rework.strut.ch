<script setup>
import PageHeader from '@/components/layout/PageHeader.vue'
import SidebarLayout from '@/components/ui/form/SidebarLayout.vue'
import FormActions from '@/components/ui/form/FormActions.vue'

/**
 * Page frame for a resource form (header, save/cancel, main + sidebar slots).
 * Use together with useResourceForm().
 */
defineProps({
	title: { type: String, required: true },
	loading: { type: Boolean, default: false },
	isEdit: { type: Boolean, default: false },
	sidebar: { type: Boolean, default: true },
})

const emit = defineEmits(['submit', 'cancel'])
</script>

<template>
	<div>
		<PageHeader :title="title" />
		<div v-if="loading" class="text-sm text-gray-400 dark:text-warm-500">Laden...</div>
		<form v-else @submit.prevent="emit('submit')">
			<div v-if="!sidebar" class="flex flex-col gap-24">
				<slot />
			</div>
			<SidebarLayout v-else>
				<div class="flex flex-col gap-24">
					<slot />
				</div>
				<template #sidebar>
					<div class="flex flex-col gap-24">
						<slot name="sidebar" />
					</div>
				</template>
			</SidebarLayout>
			<FormActions :submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'" cancelLabel="Abbrechen" @cancel="emit('cancel')" />
		</form>
	</div>
</template>
