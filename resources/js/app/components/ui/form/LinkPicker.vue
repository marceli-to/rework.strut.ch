<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import FormSelect from './FormSelect.vue'
import projectsApi from '@/api/projects'

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({ type: null, url: null }),
	},
})

const emit = defineEmits(['update:modelValue'])

const linkType = ref(props.modelValue?.type || null)
const linkPage = ref(props.modelValue?.type === 'page' ? props.modelValue.url : null)
const linkProject = ref(props.modelValue?.type === 'project' ? props.modelValue.url : null)

const projects = ref([])

const typeOptions = [
	{ value: 'page', label: 'Seite' },
	{ value: 'project', label: 'Projekt' },
]

const pageOptions = [
	{ value: '/', label: 'Home' },
	{ value: '/projekte/auswahl', label: 'Projekte' },
	{ value: '/projekte/werkliste', label: 'Werkliste' },
	{ value: '/atelier/profil', label: 'Profil' },
	{ value: '/atelier/team', label: 'Team' },
	{ value: '/atelier/jobs', label: 'Jobs' },
	{ value: '/kontakt', label: 'Kontakt' },
]

const projectOptions = computed(() =>
	[...projects.value]
		.sort((a, b) => a.title.localeCompare(b.title, 'de'))
		.map(p => ({
			value: `/projekte/auswahl/${p.slug}/bilder`,
			label: p.location ? `${p.title}, ${p.location}` : p.title,
		}))
)

onMounted(async () => {
	const { data } = await projectsApi.featured()
	projects.value = data
})

watch(() => props.modelValue, (val) => {
	linkType.value = val?.type || null
	linkPage.value = val?.type === 'page' ? val.url : null
	linkProject.value = val?.type === 'project' ? val.url : null
}, { deep: true })

watch(linkType, (val) => {
	if (val !== 'page') linkPage.value = null
	if (val !== 'project') linkProject.value = null
	emitValue()
})

watch([linkPage, linkProject], emitValue)

function emitValue() {
	if (!linkType.value) {
		emit('update:modelValue', { type: null, url: null })
		return
	}
	const url = linkType.value === 'page' ? linkPage.value : linkProject.value
	emit('update:modelValue', { type: linkType.value, url: url || null })
}
</script>

<template>
	<div class="flex flex-col gap-8 lg:flex-row lg:gap-16">
		<div class="lg:flex-1">
			<FormSelect v-model="linkType" :options="typeOptions" placeholder="Kein Link" />
		</div>

		<div v-if="linkType === 'page'" class="lg:flex-1">
			<FormSelect v-model="linkPage" :options="pageOptions" placeholder="Seite wählen" />
		</div>

		<div v-if="linkType === 'project'" class="lg:flex-1">
			<FormSelect v-model="linkProject" :options="projectOptions" placeholder="Projekt wählen" />
		</div>
	</div>
</template>
