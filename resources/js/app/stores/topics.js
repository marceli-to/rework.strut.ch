import { defineStore } from 'pinia'
import topicsApi from '@/api/topics'

export const useTopicStore = defineStore('topics', {
	state: () => ({
		topics: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchTopics() {
			this.loading = true
			try {
				const { data } = await topicsApi.index()
				this.topics = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchTopic(id) {
			this.loading = true
			try {
				const { data } = await topicsApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveTopic(form, id = null) {
			this.errors = {}
			try {
				if (id) {
					await topicsApi.update(id, form)
				} else {
					await topicsApi.store(form)
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
			const topic = this.topics.find(t => t.uuid === id)
			if (topic) topic.publish = !topic.publish
			try {
				const { data } = await topicsApi.toggle(id)
				const idx = this.topics.findIndex(t => t.uuid === id)
				if (idx !== -1) this.topics[idx] = data.data
			} catch {
				if (topic) topic.publish = !topic.publish
			}
		},

		async deleteTopic(id) {
			await topicsApi.destroy(id)
			this.topics = this.topics.filter(t => t.uuid !== id)
		},

		async reorderTopics(items) {
			await topicsApi.reorder(items)
		},
	},
})
