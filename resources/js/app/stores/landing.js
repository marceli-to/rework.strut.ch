import { defineStore } from 'pinia'
import landingApi from '@/api/landing'

export const useLandingStore = defineStore('landing', {
	state: () => ({
		slides: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchSlides() {
			this.loading = true
			try {
				const { data } = await landingApi.index()
				this.slides = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchSlide(uuid) {
			this.loading = true
			try {
				const { data } = await landingApi.show(uuid)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveSlide(form, uuid = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (uuid) {
					await landingApi.update(uuid, payload)
				} else {
					await landingApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async toggle(uuid) {
			const slide = this.slides.find(s => s.uuid === uuid)
			if (slide) slide.publish = !slide.publish
			try {
				const { data } = await landingApi.toggle(uuid)
				const idx = this.slides.findIndex(s => s.uuid === uuid)
				if (idx !== -1) this.slides[idx] = data.data
			} catch {
				if (slide) slide.publish = !slide.publish
			}
		},

		async deleteSlide(uuid) {
			await landingApi.destroy(uuid)
			this.slides = this.slides.filter(s => s.uuid !== uuid)
		},
	},
})
