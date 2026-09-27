import { defineStore } from 'pinia'
import mediaApi from '@/api/media'

export const useMediaStore = defineStore('media', {
	state: () => ({
		items: [],
		loading: false,
		errors: {},
	}),

	getters: {
		tempItems: (state) => {
			return state.items.filter(item => item._temp)
		},
	},

	actions: {
		setItems(items) {
			this.items = items || []
		},

		addItem(item) {
			this.items.push(item)
		},

		async updateItem(uuid, data) {
			const index = this.items.findIndex(i => i.uuid === uuid)
			if (index === -1) return false

			const item = this.items[index]

			if (item._temp) {
				this.items[index] = { ...item, ...data }
				return true
			}

			this.errors = {}
			try {
				const { data: response } = await mediaApi.update(uuid, data)
				this.items[index] = response.data
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async deleteItem(uuid) {
			const item = this.items.find(i => i.uuid === uuid)
			if (item && !item._temp) {
				await mediaApi.destroy(uuid)
			}
			this.items = this.items.filter(i => i.uuid !== uuid)
		},

		async reorder(items) {
			const hasPersistedItems = items.some(i => !i._temp)
			this.items = items

			if (hasPersistedItems) {
				const reorderData = items
					.filter(i => !i._temp)
					.map((item, index) => ({
						uuid: item.uuid,
						sort_order: index,
					}))
				await mediaApi.reorder(reorderData)
			}
		},

		async setTeaser(uuid) {
			const item = this.items.find(i => i.uuid === uuid)
			const wasTeaser = item?.is_teaser

			if (item?._temp) {
				this.items = this.items.map(i => ({
					...i,
					is_teaser: wasTeaser ? false : i.uuid === uuid,
				}))
				return
			}

			await mediaApi.teaser(uuid)
			this.items = this.items.map(i => ({
				...i,
				is_teaser: wasTeaser ? false : i.uuid === uuid,
			}))
		},

		async setOg(uuid) {
			const item = this.items.find(i => i.uuid === uuid)
			const wasOg = item?.is_og

			if (item?._temp) {
				this.items = this.items.map(i => ({
					...i,
					is_og: wasOg ? false : i.uuid === uuid,
				}))
				return
			}

			await mediaApi.og(uuid)
			this.items = this.items.map(i => ({
				...i,
				is_og: wasOg ? false : i.uuid === uuid,
			}))
		},

		async setCrop(uuid, cropData) {
			const index = this.items.findIndex(i => i.uuid === uuid)
			if (index === -1) return false

			const item = this.items[index]

			if (item._temp) {
				this.items[index] = { ...item, crop: cropData }
				return true
			}

			this.errors = {}
			try {
				const { data: response } = await mediaApi.crop(uuid, cropData)
				this.items[index] = response.data
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
