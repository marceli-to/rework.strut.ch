import api from './axios'

/**
 * Endpoints of a ResourceController (routes/api.php → $resource()).
 */
export function createResourceApi(prefix) {
	return {
		index: (params = {}) => api.get(prefix, { params }),
		show: (uuid) => api.get(`${prefix}/${uuid}`),
		store: (data) => api.post(prefix, data),
		update: (uuid, data) => api.put(`${prefix}/${uuid}`, data),
		toggle: (uuid) => api.patch(`${prefix}/${uuid}/publish`),
		destroy: (uuid) => api.delete(`${prefix}/${uuid}`),
		reorder: (items) => api.patch(`${prefix}/reorder`, { items }),
	}
}

export const projectsApi = createResourceApi('/projects')
export const categoriesApi = createResourceApi('/categories')
export const categoryTypesApi = createResourceApi('/category-types')
export const pagesApi = createResourceApi('/pages')
export const newsApi = createResourceApi('/news')
export const teamApi = createResourceApi('/team')
export const jobsApi = createResourceApi('/jobs')
export const booksApi = createResourceApi('/books')
export const entriesApi = createResourceApi('/entries')

export const optionsApi = {
	get: () => api.get('/options'),
}
