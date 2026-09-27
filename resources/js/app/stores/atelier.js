import { defineStore } from 'pinia'
import atelierApi from '@/api/atelier'

export const useAtelierStore = defineStore('atelier', {
	state: () => ({
		pages: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchPages() {
			this.loading = true
			try {
				const { data } = await atelierApi.index()
				this.pages = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchPage(id) {
			this.loading = true
			try {
				const { data } = await atelierApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async savePage(form, id, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				await atelierApi.update(id, payload)
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async toggle(id) {
			const page = this.pages.find(p => p.uuid === id)
			if (page) page.publish = !page.publish
			try {
				const { data } = await atelierApi.toggle(id)
				const idx = this.pages.findIndex(p => p.uuid === id)
				if (idx !== -1) this.pages[idx] = data.data
			} catch {
				if (page) page.publish = !page.publish
			}
		},
	},
})
