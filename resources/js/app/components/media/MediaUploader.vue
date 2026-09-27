<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { PhPlus, PhUploadSimple } from '@phosphor-icons/vue'
import Uppy from '@uppy/core'
import XHRUpload from '@uppy/xhr-upload'
import German from '@uppy/locales/lib/de_DE'

const props = defineProps({
	compact: { type: Boolean, default: false },
	maxFiles: { type: Number, default: null },
})

const extensions = ['.jpg', '.jpeg', '.png', '.webp', '.gif']
const hint = 'JPG, PNG, WebP, GIF — max. 50 MB'

const emit = defineEmits(['uploaded'])

const fileInput = ref(null)
const isDragging = ref(false)
const uploading = ref(false)
const progress = ref(0)
let uppy = null

onMounted(() => {
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content

	uppy = new Uppy({
		locale: German,
		autoProceed: true,
		restrictions: {
			allowedFileTypes: extensions,
			maxFileSize: 51200 * 1024,
			maxNumberOfFiles: props.maxFiles,
		},
	})

	uppy.use(XHRUpload, {
		endpoint: '/api/dashboard/media/upload',
		fieldName: 'file',
		headers: {
			'X-CSRF-TOKEN': csrfToken,
			'Accept': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	})

	uppy.on('upload', () => {
		uploading.value = true
		progress.value = 0
	})

	uppy.on('progress', (value) => {
		progress.value = value
	})

	uppy.on('upload-success', (file, response) => {
		emit('uploaded', response.body.data)
		uppy.removeFile(file.id)
	})

	uppy.on('complete', () => {
		uploading.value = false
		progress.value = 0
	})
})

onBeforeUnmount(() => {
	if (uppy) uppy.destroy()
})

function onDrop(e) {
	isDragging.value = false
	const files = e.dataTransfer?.files
	if (files) addFiles(files)
}

function onFileSelect(e) {
	const files = e.target?.files
	if (files) addFiles(files)
	if (fileInput.value) fileInput.value.value = ''
}

function addFiles(fileList) {
	for (const file of fileList) {
		try {
			uppy.addFile({ name: file.name, type: file.type, data: file })
		} catch (err) {}
	}
}
</script>

<template>
	<div>
		<!-- Compact: slim add bar -->
		<div
			v-if="compact"
			class="border border-gray-200 dark:border-warm-700 rounded-md transition-colors cursor-pointer hover:ring-2 hover:ring-gray-200 dark:hover:ring-warm-700 hover:border-gray-300 dark:hover:border-warm-600 bg-white dark:bg-warm-800"
			:class="isDragging ? '!border-gray-900 dark:!border-warm-400 !bg-gray-50 dark:!bg-warm-700' : ''"
			@click="fileInput?.click()"
			@dragover.prevent="isDragging = true"
			@dragleave.prevent="isDragging = false"
			@drop.prevent="onDrop"
		>
			<div class="flex items-center justify-center gap-8 py-24">
				<PhPlus :size="14" weight="light" class="text-gray-400 dark:text-warm-500" />
				<span class="text-xs text-gray-500 dark:text-warm-400">Bilder hinzufügen</span>
				<span class="text-xs text-gray-400 dark:text-warm-500 ml-4">{{ hint }}</span>
			</div>
		</div>

		<!-- Full: drop zone -->
		<div
			v-else
			class="border border-gray-200 dark:border-warm-700 rounded-md transition-colors cursor-pointer hover:ring-2 hover:ring-gray-200 dark:hover:ring-warm-700 hover:border-gray-300 dark:hover:border-warm-600 bg-white dark:bg-warm-800"
			:class="isDragging ? '!border-gray-900 dark:!border-warm-400 !bg-gray-50 dark:!bg-warm-700' : ''"
			@click="fileInput?.click()"
			@dragover.prevent="isDragging = true"
			@dragleave.prevent="isDragging = false"
			@drop.prevent="onDrop"
		>
			<div class="flex flex-col items-center justify-center py-[8rem] px-24">
				<PhUploadSimple :size="24" weight="light" class="text-gray-400 dark:text-warm-500 mb-12" />
				<p class="text-xs text-gray-500 dark:text-warm-400">
					<span class="text-gray-900 dark:text-warm-100 underline decoration-gray-300 dark:decoration-warm-600 underline-offset-4">Dateien auswählen</span>
					oder hierhin ziehen
				</p>
				<p class="text-xs text-gray-400 dark:text-warm-500 mt-8">{{ hint }}</p>
			</div>
		</div>

		<!-- Progress -->
		<div v-if="uploading" class="mt-8">
			<div class="h-2 bg-gray-200 dark:bg-warm-700 overflow-hidden rounded-full">
				<div class="h-full bg-gray-900 dark:bg-warm-100 transition-all duration-300" :style="{ width: progress + '%' }" />
			</div>
		</div>

		<input
			ref="fileInput"
			type="file"
			:multiple="!maxFiles || maxFiles > 1"
			:accept="extensions.join(',')"
			class="hidden"
			@change="onFileSelect"
		/>
	</div>
</template>
