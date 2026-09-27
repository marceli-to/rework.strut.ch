import api from './axios'

export default {
	index: () => api.get('/media'),
	upload: (data) => api.post('/media/upload', data, {
		headers: { 'Content-Type': 'multipart/form-data' },
	}),
	update: (uuid, data) => api.put(`/media/${uuid}`, data),
	destroy: (uuid) => api.delete(`/media/${uuid}`),
	reorder: (items) => api.patch('/media/reorder', { items }),
	teaser: (uuid) => api.patch(`/media/${uuid}/teaser`),
	og: (uuid) => api.patch(`/media/${uuid}/og`),
	crop: (uuid, data) => api.patch(`/media/${uuid}/crop`, data),
}
