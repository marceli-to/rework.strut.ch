<script setup>
import { computed } from 'vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import Editor from '@/components/ui/editor/Editor.vue'

/**
 * Label + control + validation error for one form field.
 * type: text | number | email | url | textarea | select | editor | checkbox
 */
const model = defineModel()

const props = defineProps({
	name: { type: String, required: true },
	label: { type: String, required: true },
	type: { type: String, default: 'text' },
	errors: { type: Object, default: () => ({}) },
	options: { type: Array, default: () => [] },
	placeholder: { type: String, default: null },
	hint: { type: String, default: null },
	rows: { type: [String, Number], default: 3 },
	required: { type: Boolean, default: false },
})

const error = computed(() => props.errors[props.name])
const clear = () => delete props.errors[props.name]
</script>

<template>
	<FormGroup>
		<FormCheckbox v-if="type === 'checkbox'" :id="name" v-model="model">{{ label }}</FormCheckbox>
		<template v-else>
			<FormLabel :for="name" :error="error">{{ label }}{{ required ? ' *' : '' }}</FormLabel>
			<Editor v-if="type === 'editor'" v-model="model" :hasError="!!error" @focus="clear" />
			<FormTextarea v-else-if="type === 'textarea'" :id="name" v-model="model" :rows="rows" :placeholder="placeholder" class="field-sizing-content" :hasError="!!error" @focus="clear" />
			<FormSelect v-else-if="type === 'select'" :id="name" v-model="model" :options="options" :hasError="!!error" @focus="clear" />
			<FormInput v-else :id="name" v-model="model" :type="type" :placeholder="placeholder" :hasError="!!error" @focus="clear" />
		</template>
		<span v-if="hint" class="block text-xs text-gray-500 dark:text-warm-400 mt-6">{{ hint }}</span>
	</FormGroup>
</template>
