import api from './axios'

/**
 * Shared grid API for a context ('project' | 'home') and owner (project uuid | 'home').
 */
export function createGridApi(context, owner) {
	const base = `/grids/${context}/${owner}`

	return {
		show: () => api.get(base),
		options: () => api.get(`${base}/options`),
		storeRow: (data) => api.post(`${base}/rows`, data),
		updateRow: (row, data) => api.put(`${base}/rows/${row}`, data),
		destroyRow: (row) => api.delete(`${base}/rows/${row}`),
		reorderRows: (items) => api.patch(`${base}/rows/reorder`, { items }),
		setItem: (row, position, data) => api.put(`${base}/rows/${row}/items/${position}`, data),
		destroyItem: (row, position) => api.delete(`${base}/rows/${row}/items/${position}`),
		moveItem: (data) => api.patch(`${base}/items/move`, data),
	}
}
