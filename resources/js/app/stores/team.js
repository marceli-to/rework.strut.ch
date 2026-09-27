import { defineStore } from 'pinia'
import teamApi from '@/api/team'

export const useTeamStore = defineStore('team', {
	state: () => ({
		members: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchMembers() {
			this.loading = true
			try {
				const { data } = await teamApi.index()
				this.members = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchMember(id) {
			this.loading = true
			try {
				const { data } = await teamApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveMember(form, id = null) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (id) {
					await teamApi.update(id, payload)
				} else {
					await teamApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async toggle(id) {
			const member = this.members.find(m => m.uuid === id)
			if (member) member.publish = !member.publish
			try {
				const { data } = await teamApi.toggle(id)
				const idx = this.members.findIndex(m => m.uuid === id)
				if (idx !== -1) this.members[idx] = data.data
			} catch {
				if (member) member.publish = !member.publish
			}
		},

		async deleteMember(id) {
			await teamApi.destroy(id)
			this.members = this.members.filter(m => m.uuid !== id)
		},

		async reorderMembers(items) {
			await teamApi.reorder(items)
		},
	},
})
