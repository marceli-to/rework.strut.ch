<script setup>
import AppSidebar from '@/components/layout/AppSidebar.vue'
import ToastContainer from '@/components/ui/toast/Container.vue'
import ConfirmDialog from '@/components/ui/dialog/ConfirmDialog.vue'
import { useConfirm } from '@/composables/useConfirm'
const { state: confirmState, onConfirm, onCancel } = useConfirm()
</script>

<template>
	<div class="min-h-dvh bg-gray-100 dark:bg-warm-950 flex">
		<AppSidebar />
		<div class="ml-240 flex-1 p-16">
			<main class="min-h-[calc(100dvh-2rem)] bg-white dark:bg-warm-900 rounded-2xl p-24 pb-32 relative">
				<ToastContainer />
				<slot />
			</main>
		</div>
		<ConfirmDialog
			:open="confirmState.open"
			:title="confirmState.title"
			:message="confirmState.message"
			:confirm-label="confirmState.confirmLabel"
			:cancel-label="confirmState.cancelLabel"
			:destructive="confirmState.destructive"
			@confirm="onConfirm"
			@cancel="onCancel" />
	</div>
</template>
