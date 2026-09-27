import { defineStore } from 'pinia'

/**
 * One store shape for every content module (list, current, errors).
 */
export function defineResourceStore(id, resourceApi) {
	return defineStore(id, {
		state: () => ({
			items: [],
			current: null,
			loading: false,
			errors: {},
		}),

		actions: {
			async fetchAll(params = {}) {
				this.loading = true
				try {
					const { data } = await resourceApi.index(params)
					this.items = data.data
				} finally {
					this.loading = false
				}
			},

			async fetchOne(uuid) {
				this.loading = true
				try {
					const { data } = await resourceApi.show(uuid)
					this.current = data.data
				} finally {
					this.loading = false
				}
			},

			async save(form, uuid = null, media = []) {
				this.errors = {}
				try {
					const payload = { ...form, media }
					const { data } = uuid
						? await resourceApi.update(uuid, payload)
						: await resourceApi.store(payload)
					this.current = data.data
					return data.data
				} catch (error) {
					if (error.response?.status === 422) {
						this.errors = error.response.data.errors
					}
					return null
				}
			},

			async toggle(uuid) {
				const item = this.items.find(i => i.uuid === uuid)
				if (item) item.publish = !item.publish
				try {
					await resourceApi.toggle(uuid)
				} catch {
					if (item) item.publish = !item.publish
				}
			},

			async destroy(uuid) {
				await resourceApi.destroy(uuid)
				this.items = this.items.filter(i => i.uuid !== uuid)
			},

			async reorder(items) {
				this.items = items
				await resourceApi.reorder(items.map((item, index) => ({ uuid: item.uuid, sort_order: index })))
			},
		},
	})
}
