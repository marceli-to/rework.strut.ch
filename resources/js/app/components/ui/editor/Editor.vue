<script setup>
import { watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Toolbar from './Toolbar.vue'

const props = defineProps({
	modelValue: { type: String, default: '' },
	hasError: { type: Boolean, default: false },
	headings: { type: Boolean, default: false },
	link: { type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue', 'focus'])

const editor = useEditor({
	content: props.modelValue,
	extensions: [
		StarterKit.configure({
			heading: props.headings ? { levels: [2, 3] } : false,
			codeBlock: false,
			blockquote: false,
			code: false,
			horizontalRule: false,
			link: false,
		}),
		Link.configure({
			openOnClick: false,
			HTMLAttributes: {
				target: null,
				title: null,
			},
		}),
	],
	onUpdate({ editor }) {
		emit('update:modelValue', editor.getHTML())
	},
	onFocus() {
		emit('focus')
	},
})

watch(() => props.modelValue, (value) => {
	if (!editor.value) return
	if (editor.value.getHTML() === value) return
	editor.value.commands.setContent(value, false)
})
</script>

<template>
	<div class="editor" :class="{ 'editor--error': hasError }">
		<Toolbar v-if="editor" :editor="editor" :headings="headings" :link="link" />
		<EditorContent :editor="editor" />
	</div>
</template>
