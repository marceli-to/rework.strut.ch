import { reactive } from 'vue'

const state = reactive({
	open: false,
	title: 'Bestätigen',
	message: 'Sind Sie sicher?',
	confirmLabel: 'Bestätigen',
	cancelLabel: 'Abbrechen',
	destructive: false,
	resolve: null,
})

export function useConfirm() {
	function confirm(options = {}) {
		return new Promise((resolve) => {
			Object.assign(state, {
				open: true,
				title: options.title ?? 'Bestätigen',
				message: options.message ?? 'Sind Sie sicher?',
				confirmLabel: options.confirmLabel ?? 'Bestätigen',
				cancelLabel: options.cancelLabel ?? 'Abbrechen',
				destructive: options.destructive ?? false,
				resolve,
			})
		})
	}

	function onConfirm() {
		state.open = false
		state.resolve?.(true)
	}

	function onCancel() {
		state.open = false
		state.resolve?.(false)
	}

	return { state, confirm, onConfirm, onCancel }
}
