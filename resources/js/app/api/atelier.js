import api from './axios'

export default {
	index: () => api.get('/atelier'),
	show: (id) => api.get(`/atelier/${id}`),
	update: (id, data) => api.put(`/atelier/${id}`, data),
	toggle: (id) => api.patch(`/atelier/${id}/publish`),
}
