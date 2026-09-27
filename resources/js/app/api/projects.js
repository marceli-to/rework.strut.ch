import api from './axios'

export default {
	index: () => api.get('/projects'),
	featured: () => api.get('/projects/featured'),
	show: (id) => api.get(`/projects/${id}`),
	store: (data) => api.post('/projects', data),
	update: (id, data) => api.put(`/projects/${id}`, data),
	toggle: (id) => api.patch(`/projects/${id}/publish`),
	feature: (id) => api.patch(`/projects/${id}/feature`),
	destroy: (id) => api.delete(`/projects/${id}`),
}
