import { reactive, readonly } from 'vue'

const state = reactive({
	toasts: [],
})

let nextId = 0

function add(type, message) {
	const id = nextId++
	state.toasts.push({ id, type, message })
	setTimeout(() => dismiss(id), 3000)
}

function dismiss(id) {
	const index = state.toasts.findIndex(t => t.id === id)
	if (index > -1) state.toasts.splice(index, 1)
}

function clearErrors() {
	state.toasts = state.toasts.filter(t => t.type !== 'error')
}

export function useToast() {
	return {
		toasts: readonly(state),
		success: (message) => add('success', message),
		error: (message) => add('error', message),
		dismiss,
		clearErrors,
	}
}
