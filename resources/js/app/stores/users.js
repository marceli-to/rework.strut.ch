import { defineStore } from 'pinia'
import usersApi from '@/api/users'

export const useUsersStore = defineStore('users', {
	state: () => ({
		users: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchUsers() {
			this.loading = true
			try {
				const { data } = await usersApi.index()
				this.users = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchUser(id) {
			this.loading = true
			try {
				const { data } = await usersApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveUser(form, id = null) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (id && !payload.password) {
					delete payload.password
				}
				if (id) {
					await usersApi.update(id, payload)
				} else {
					await usersApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async deleteUser(id) {
			await usersApi.destroy(id)
			this.users = this.users.filter(u => u.uuid !== id)
		},
	},
})
