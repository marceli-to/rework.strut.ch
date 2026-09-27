<script setup>
import AppDialog from './AppDialog.vue'
import FormButton from '@/components/ui/form/FormButton.vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: 'Bestätigen' },
	message: { type: String, default: 'Sind Sie sicher?' },
	confirmLabel: { type: String, default: 'Bestätigen' },
	cancelLabel: { type: String, default: 'Abbrechen' },
	destructive: { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
	<AppDialog :open="open" :title="title" @close="emit('cancel')">
		<p class="text-sm text-gray-600 dark:text-warm-400">{{ message }}</p>

		<template #footer>
			<div class="flex justify-end gap-16">
				<FormButton variant="secondary" @click="emit('cancel')">
					{{ cancelLabel }}
				</FormButton>
				<FormButton
					:variant="destructive ? 'destructive' : 'primary'"
					@click="emit('confirm')"
				>
					{{ confirmLabel }}
				</FormButton>
			</div>
		</template>
	</AppDialog>
</template>
