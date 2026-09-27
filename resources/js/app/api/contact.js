import api from './axios'

export default {
	show: () => api.get('/contact'),
	update: (data) => api.put('/contact', data),
}
