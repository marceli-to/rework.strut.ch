import { defineStore } from 'pinia'
import kontaktApi from '@/api/contact'

export const useKontaktStore = defineStore('kontakt', {
	state: () => ({
		contact: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchContact() {
			this.loading = true
			try {
				const { data } = await kontaktApi.show()
				this.contact = data.data
			} finally {
				this.loading = false
			}
		},

		async saveContact(form) {
			this.errors = {}
			try {
				await kontaktApi.update(form)
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},
	},
})
