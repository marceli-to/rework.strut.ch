<script setup>
import { computed, ref } from 'vue'
import { useMediaStore } from '@/stores/media'
import { useConfirm } from '@/composables/useConfirm'
import { useOptionsStore } from '@/stores/options'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'

/**
 * Upload + manage one media collection of the entity being edited.
 * Items live in the shared media store; new uploads are sent with the form.
 */
const props = defineProps({
	label: { type: String, default: 'Bilder' },
	collection: { type: String, default: 'images' }, // 'images' | 'files' | 'og'
	profile: { type: String, required: true }, // config/media.php: file types + crop ratios
	maxFiles: { type: Number, default: null },
	hasTeaser: { type: Boolean, default: false },
	hasOg: { type: Boolean, default: false },
})

const store = useMediaStore()
const { confirm } = useConfirm()
const editing = ref(null)

const items = computed(() => store.inCollection(props.collection))
const isFiles = computed(() => props.collection === 'files')
const crops = computed(() => useOptionsStore().media_profiles[props.profile]?.crops ?? [])
const full = computed(() => props.maxFiles && items.value.length >= props.maxFiles)

function onUploaded(media) {
	store.addItem({ ...media, collection: props.collection })
}

async function onSave({ uuid, data }) {
	if (await store.updateItem(uuid, data)) editing.value = null
}

async function onDelete(media) {
	const ok = await confirm({
		title: 'Datei löschen',
		message: `"${media.original_name}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (ok) await store.deleteItem(media.uuid)
}
</script>

<template>
	<div>
		<FormLabel>{{ label }}</FormLabel>
		<div class="mt-8 flex flex-col gap-16">
			<MediaUploader
				v-if="!full"
				:profile="profile"
				:label="isFiles ? 'Dateien hinzufügen' : maxFiles === 1 ? 'Bild hinzufügen' : 'Bilder hinzufügen'"
				:maxFiles="maxFiles"
				:compact="items.length > 0"
				@uploaded="onUploaded"
			/>
			<MediaGrid
				v-if="items.length"
				:items="items"
				sidebar
				:hasTeaser="hasTeaser"
				:hasOg="hasOg"
				:crops="crops"
				@edit="editing = $event"
				@delete="onDelete"
				@reorder="store.reorder($event)"
				@teaser="store.setTeaser($event.uuid)"
				@og="store.setOg($event.uuid)"
			/>
		</div>
		<MediaEdit :media="editing" @close="editing = null" @save="onSave" />
	</div>
</template>
