import { defineStore } from 'pinia'
import { optionsApi } from '@/api/resource'

/**
 * Select options (enums, categories, projects) shared by admin forms.
 */
export const useOptionsStore = defineStore('options', {
	state: () => ({
		status: [],
		competition: [],
		entry_type: [],
		categories: [],
		category_types: [],
		projects: [],
		loaded: false,
	}),

	actions: {
		async load(force = false) {
			if (this.loaded && !force) return
			const { data } = await optionsApi.get()
			Object.assign(this, data, { loaded: true })
		},
	},
})
