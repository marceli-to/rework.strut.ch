<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { PhX } from '@phosphor-icons/vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: null },
	size: { type: String, default: 'sm' }, // sm, md, lg
})

const emit = defineEmits(['close'])

const visible = ref(false)
const mounted = ref(false)
const closing = ref(false)

const sizes = {
	sm: 'max-w-[420px]',
	md: 'max-w-[560px]',
	lg: 'max-w-[720px]',
}

watch(() => props.open, (val) => {
	if (val) {
		closing.value = false
		mounted.value = true
		requestAnimationFrame(() => {
			visible.value = true
		})
	} else if (!closing.value) {
		visible.value = false
		setTimeout(() => {
			mounted.value = false
		}, 200)
	}
}, { immediate: true })

function close() {
	if (closing.value) return
	closing.value = true
	visible.value = false
	setTimeout(() => {
		mounted.value = false
		emit('close')
	}, 200)
}

function onKeydown(e) {
	if (e.key === 'Escape' && props.open) {
		document.activeElement?.blur()
		close()
	}
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
	<Teleport to="body">
		<div v-if="mounted" class="fixed inset-0 z-50">
			<!-- Backdrop -->
			<Transition name="fade">
				<div
					v-if="visible"
					class="absolute inset-0 bg-black/30"
					@click="close"
				/>
			</Transition>

			<!-- Panel -->
			<div
				class="absolute top-16 right-16 bottom-16 w-full bg-white dark:bg-warm-900 rounded-2xl shadow-xl dark:shadow-none transition-transform duration-200 ease-out flex flex-col"
				:class="[sizes[size], visible ? 'translate-x-0' : 'translate-x-full']"
			>
				<!-- Header -->
				<div class="flex items-center justify-between px-24 py-20 border-b border-neutral-200 dark:border-warm-800">
					<slot name="header">
						<h3 class="text-sm font-medium text-gray-900 dark:text-warm-100">{{ title }}</h3>
					</slot>
					<button
						type="button"
						class="text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer"
						@click="close"
					>
						<PhX :size="16" weight="light" />
					</button>
				</div>

				<!-- Content -->
				<div class="flex-1 overflow-y-auto">
					<slot />
				</div>

				<!-- Footer -->
				<div v-if="$slots.footer" class="px-24 pt-16 pb-24">
					<slot name="footer" />
				</div>
			</div>
		</div>
	</Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}
</style>
