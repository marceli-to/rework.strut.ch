import api from './axios'

export default {
    show: () => api.get('/seo'),
    update: (data) => api.put('/seo', data),
}
