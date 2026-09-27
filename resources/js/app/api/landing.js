import api from './axios'

export default {
	index: () => api.get('/landing'),
	show: (id) => api.get(`/landing/${id}`),
	store: (data) => api.post('/landing', data),
	update: (id, data) => api.put(`/landing/${id}`, data),
	toggle: (id) => api.patch(`/landing/${id}/publish`),
	destroy: (id) => api.delete(`/landing/${id}`),
	reorder: (items) => api.patch('/landing/reorder', { items }),
}
