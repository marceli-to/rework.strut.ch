import api from './axios'

export default {
	index: () => api.get('/jobs'),
	show: (id) => api.get(`/jobs/${id}`),
	store: (data) => api.post('/jobs', data),
	update: (id, data) => api.put(`/jobs/${id}`, data),
	toggle: (id) => api.patch(`/jobs/${id}/publish`),
	destroy: (id) => api.delete(`/jobs/${id}`),
	reorder: (items) => api.patch('/jobs/reorder', { items }),
}
