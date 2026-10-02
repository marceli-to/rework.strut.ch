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

		// New uploads as sent with the entity form (HasMediaRules)
		payload: (state) => state.items.filter(item => item._temp).map(item => ({
			uuid: item.uuid,
			collection: item.collection || 'images',
			file: item.file,
			original_name: item.original_name,
			mime_type: item.mime_type,
			size: item.size,
			width: item.width,
			height: item.height,
			alt: item.alt || null,
			caption: item.caption || null,
			crop: item.crop || null,
			variant: item.variant || 'desktop',
			is_og: !!item.is_og,
		})),

		inCollection: (state) => (collection) => state.items.filter(item => (item.collection || 'images') === collection),
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
			const collection = items[0]?.collection || 'images'
			const hasPersistedItems = items.some(i => !i._temp)
			this.items = [...this.items.filter(i => (i.collection || 'images') !== collection), ...items]

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

		async setOg(uuid) {
			const item = this.items.find(i => i.uuid === uuid)
			const wasOg = item?.is_og

			if (item?._temp) {
				this.items = this.items.map(i => ({
					...i,
					is_og: i.collection === item.collection ? (wasOg ? false : i.uuid === uuid) : i.is_og,
				}))
				return
			}

			await mediaApi.og(uuid)
			this.items = this.items.map(i => ({
				...i,
				is_og: i.collection === item.collection ? (wasOg ? false : i.uuid === uuid) : i.is_og,
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
