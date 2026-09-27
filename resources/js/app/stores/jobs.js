import { defineStore } from 'pinia'
import jobsApi from '@/api/jobs'

export const useJobStore = defineStore('jobs', {
	state: () => ({
		jobs: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchJobs() {
			this.loading = true
			try {
				const { data } = await jobsApi.index()
				this.jobs = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchJob(id) {
			this.loading = true
			try {
				const { data } = await jobsApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveJob(form, id = null) {
			this.errors = {}
			try {
				if (id) {
					await jobsApi.update(id, form)
				} else {
					await jobsApi.store(form)
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
			const job = this.jobs.find(j => j.uuid === id)
			if (job) job.publish = !job.publish
			try {
				const { data } = await jobsApi.toggle(id)
				const idx = this.jobs.findIndex(j => j.uuid === id)
				if (idx !== -1) this.jobs[idx] = data.data
			} catch {
				if (job) job.publish = !job.publish
			}
		},

		async deleteJob(id) {
			await jobsApi.destroy(id)
			this.jobs = this.jobs.filter(j => j.uuid !== id)
		},

		async reorderJobs(items) {
			await jobsApi.reorder(items)
		},
	},
})
