import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'

/**
 * Create/edit form for a resource store: loads the record into `form`,
 * tracks unsaved changes, sends new media along and redirects back.
 *
 * @param store       resource store (stores/resources.js)
 * @param defaults    empty form values
 * @param options     { indexRoute, editRoute, stay, labels: { created, updated }, fromRecord(record), toPayload(form), afterLoad() }
 *                    stay: keep editing after save (a new record switches to editRoute)
 */
export function useResourceForm(store, defaults, options = {}) {
	const route = useRoute()
	const router = useRouter()
	const mediaStore = useMediaStore()
	const toast = useToast()

	const uuid = computed(() => route.params.id || options.uuid?.() || null)
	const isEdit = computed(() => !!uuid.value)
	const form = ref({ ...defaults })
	const { setOriginal, bypass } = useUnsavedChanges(form)

	function fill(record) {
		const values = options.fromRecord ? options.fromRecord(record) : record
		form.value = Object.fromEntries(Object.keys(defaults).map(key => [key, values[key] ?? defaults[key]]))
		mediaStore.setItems(record.media || [])
	}

	onMounted(async () => {
		mediaStore.setItems([])
		if (isEdit.value) {
			await store.fetchOne(uuid.value)
			if (store.current) fill(store.current)
		}
		await options.afterLoad?.()
		setOriginal()
	})

	async function submit() {
		const payload = options.toPayload ? options.toPayload(form.value) : form.value
		const saved = await store.save(payload, uuid.value, mediaStore.payload)

		if (!saved) {
			if (Object.keys(store.errors).length) toast.error('Bitte überprüfen Sie das Formular')
			return null
		}

		toast.success(isEdit.value ? (options.labels?.updated ?? 'Gespeichert') : (options.labels?.created ?? 'Erstellt'))

		if (options.stay && isEdit.value) {
			fill(saved)
			setOriginal()
		} else if (options.stay && options.editRoute) {
			bypass()
			router.replace({ name: options.editRoute, params: { id: saved.uuid } })
		} else {
			bypass()
			back()
		}

		return saved
	}

	function back() {
		const target = typeof options.indexRoute === 'function' ? options.indexRoute(form.value) : options.indexRoute
		router.push(target)
	}

	function clearError(field) {
		delete store.errors[field]
	}

	function cancel() {
		back()
	}

	return { form, isEdit, uuid, submit, cancel, clearError, errors: computed(() => store.errors), loading: computed(() => store.loading) }
}
