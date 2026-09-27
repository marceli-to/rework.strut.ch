<script setup>
import { ref, onMounted } from 'vue'
import { useSeoStore } from '@/stores/seo'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import { useUnsavedChanges } from '@/composables/useUnsavedChanges'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const store = useSeoStore()
const mediaStore = useMediaStore()
const toast = useToast()

const form = ref({
    landing_meta_description: '',
    projects_meta_description: '',
    werkliste_meta_description: '',
    profile_meta_description: '',
    team_meta_description: '',
    jobs_meta_description: '',
    contact_meta_description: '',
})

const { setOriginal } = useUnsavedChanges(form)

onMounted(async () => {
    mediaStore.setItems([])
    await store.fetchSeo()
    if (store.seo) {
        const s = store.seo
        form.value = {
            landing_meta_description: s.landing_meta_description || '',
            projects_meta_description: s.projects_meta_description || '',
            werkliste_meta_description: s.werkliste_meta_description || '',
            profile_meta_description: s.profile_meta_description || '',
            team_meta_description: s.team_meta_description || '',
            jobs_meta_description: s.jobs_meta_description || '',
            contact_meta_description: s.contact_meta_description || '',
        }
        mediaStore.setItems(s.media || [])
    }

    setOriginal()
})

async function handleSubmit() {
    const tempMedia = mediaStore.tempItems.map(item => ({
        uuid: item.uuid,
        file: item.file,
        original_name: item.original_name,
        mime_type: item.mime_type,
        size: item.size,
        width: item.width,
        height: item.height,
        alt: item.alt || null,
        caption: item.caption || null,
        is_og: true,
    }))

    const payload = { ...form.value }
    if (tempMedia.length) {
        payload.media = tempMedia
    }

    const success = await store.saveSeo(payload)
    if (success) {
        mediaStore.setItems(store.seo?.media || [])
        setOriginal()
        toast.success('SEO-Einstellungen aktualisiert')
    } else if (Object.keys(store.errors).length) {
        toast.error('Bitte überprüfen Sie das Formular')
    }
}

function onUploaded(media) { mediaStore.addItem(media) }
async function onDeleteMedia(media) { await mediaStore.deleteItem(media.uuid) }
</script>

<template>
    <div>
        <PageHeader title="SEO" />

        <div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
            Laden...
        </div>

        <form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">

            <FormGroup>
                <FormLabel>Open Graph Bild</FormLabel>
                <div class="mt-8 flex flex-col gap-16">
                    <MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
                    <MediaGrid
                        v-else
                        :items="mediaStore.items"
                        :hasCrop="false"
                        :hasEdit="false"
                        @delete="onDeleteMedia"
                    />
                </div>
            </FormGroup>

            <div class="flex flex-col gap-4">
                <h2>Meta Descriptions</h2>
                <p class="text-sm text-gray-400 dark:text-warm-500">Kurze Beschreibung der jeweiligen Seite für Suchmaschinen. Idealerweise 1–2 Sätze, max. 160 Zeichen.</p>
            </div>

            <FormGroup>
                <FormLabel for="landing_meta_description" :error="store.errors.landing_meta_description">Startseite</FormLabel>
                <FormTextarea id="landing_meta_description" v-model="form.landing_meta_description" :hasError="!!store.errors.landing_meta_description" @focus="delete store.errors.landing_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="projects_meta_description" :error="store.errors.projects_meta_description">Projekte (Übersicht)</FormLabel>
                <FormTextarea id="projects_meta_description" v-model="form.projects_meta_description" :hasError="!!store.errors.projects_meta_description" @focus="delete store.errors.projects_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="werkliste_meta_description" :error="store.errors.werkliste_meta_description">Werkliste</FormLabel>
                <FormTextarea id="werkliste_meta_description" v-model="form.werkliste_meta_description" :hasError="!!store.errors.werkliste_meta_description" @focus="delete store.errors.werkliste_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="profile_meta_description" :error="store.errors.profile_meta_description">Profil</FormLabel>
                <FormTextarea id="profile_meta_description" v-model="form.profile_meta_description" :hasError="!!store.errors.profile_meta_description" @focus="delete store.errors.profile_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="team_meta_description" :error="store.errors.team_meta_description">Team</FormLabel>
                <FormTextarea id="team_meta_description" v-model="form.team_meta_description" :hasError="!!store.errors.team_meta_description" @focus="delete store.errors.team_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="jobs_meta_description" :error="store.errors.jobs_meta_description">Jobs</FormLabel>
                <FormTextarea id="jobs_meta_description" v-model="form.jobs_meta_description" :hasError="!!store.errors.jobs_meta_description" @focus="delete store.errors.jobs_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="contact_meta_description" :error="store.errors.contact_meta_description">Kontakt</FormLabel>
                <FormTextarea id="contact_meta_description" v-model="form.contact_meta_description" :hasError="!!store.errors.contact_meta_description" @focus="delete store.errors.contact_meta_description" />
            </FormGroup>

            <FormActions submitLabel="Speichern" />
        </form>
    </div>
</template>
