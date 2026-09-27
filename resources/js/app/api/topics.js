import api from './axios'

export default {
	index: () => api.get('/topics'),
	show: (id) => api.get(`/topics/${id}`),
	store: (data) => api.post('/topics', data),
	update: (id, data) => api.put(`/topics/${id}`, data),
	toggle: (id) => api.patch(`/topics/${id}/publish`),
	destroy: (id) => api.delete(`/topics/${id}`),
	reorder: (items) => api.patch('/topics/reorder', { items }),
}
