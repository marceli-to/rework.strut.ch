<script setup>
import { computed, ref } from 'vue'
import draggable from 'vuedraggable'
import MediaCard from '@/components/media/MediaCard.vue'
import MediaCrop from '@/components/media/MediaCrop.vue'
import { useMediaStore } from '@/stores/media'

const props = defineProps({
	items: { type: Array, default: () => [] },
	hasOg: { type: Boolean, default: false },
	hasTeaser: { type: Boolean, default: false },
	hasVariant: { type: Boolean, default: false },
	crops: { type: Array, default: () => [] }, // [{ label, value }] from the media profile
	hasEdit: { type: Boolean, default: true },
	sidebar: { type: Boolean, default: false },
})

const emit = defineEmits(['edit', 'delete', 'reorder', 'teaser', 'og'])

const store = useMediaStore()
const cropMedia = ref(null)

async function handleVariantToggle(media) {
  const newVariant = media.variant === 'mobile' ? 'desktop' : 'mobile'
  await store.updateItem(media.uuid, { variant: newVariant })
}

async function handleCropSave({ uuid, crop }) {
  try {
    await store.setCrop(uuid, crop)
  } finally {
    cropMedia.value = null
  }
}

const dragItems = computed({
	get: () => props.items,
	set: (value) => emit('reorder', value),
})
</script>

<template>
	<draggable
		v-model="dragItems"
		item-key="uuid"
		:class="sidebar ? 'grid grid-cols-2 sm:grid-cols-3 gap-20' : 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-20'"
		ghost-class="opacity-30"
		animation="150"
	>
		<template #item="{ element }">
			<div class="cursor-grab active:cursor-grabbing">
				<MediaCard
					:media="element"
					:showInfo="true"
					:badge="element.is_teaser ? 'Teaser' : null"
					:hasTeaser="hasTeaser"
					:isTeaser="element.is_teaser"
					:hasOg="hasOg"
					:isOg="element.is_og"
					:hasCrop="crops.length > 0 && element.mime_type?.startsWith('image/')"
					:hasEdit="hasEdit"
					:hasVariant="hasVariant"
					@edit="emit('edit', $event)"
					@teaser="emit('teaser', $event)"
					@og="emit('og', $event)"
					@delete="emit('delete', $event)"
					@variant="handleVariantToggle"
					@crop="cropMedia = $event"
				/>
			</div>
		</template>
	</draggable>

	<MediaCrop
		:media="cropMedia"
		:ratios="crops"
		@close="cropMedia = null"
		@save="handleCropSave"
	/>
</template>
