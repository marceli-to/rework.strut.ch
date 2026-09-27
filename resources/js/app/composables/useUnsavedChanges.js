import { ref, watch, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useConfirm } from '@/composables/useConfirm'

export function useUnsavedChanges(form) {
  const router = useRouter()
  const { confirm } = useConfirm()

  const original = ref(null)
  const skipGuard = ref(false)

  function setOriginal() {
    original.value = JSON.stringify(form.value)
  }

  function isDirty() {
    if (!original.value) return false
    return JSON.stringify(form.value) !== original.value
  }

  // Router navigation guard
  const removeGuard = router.beforeEach(async () => {
    if (skipGuard.value || !isDirty()) return true

    const confirmed = await confirm({
      title: 'Ungespeicherte Änderungen',
      message: 'Sie haben ungespeicherte Änderungen. Möchten Sie die Seite wirklich verlassen?',
      confirmLabel: 'Verlassen',
      cancelLabel: 'Abbrechen',
      destructive: true,
    })

    return confirmed
  })

  // Browser close/refresh guard
  function onBeforeUnload(e) {
    if (isDirty()) {
      e.preventDefault()
    }
  }

  window.addEventListener('beforeunload', onBeforeUnload)

  // Allow navigation without guard (call before programmatic router.push after save)
  function bypass() {
    skipGuard.value = true
  }

  onBeforeUnmount(() => {
    removeGuard()
    window.removeEventListener('beforeunload', onBeforeUnload)
  })

  return { setOriginal, isDirty, bypass }
}
