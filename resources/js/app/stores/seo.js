import { defineStore } from 'pinia'
import seoApi from '@/api/seo'

export const useSeoStore = defineStore('seo', {
    state: () => ({
        seo: null,
        loading: false,
        errors: {},
    }),

    actions: {
        async fetchSeo() {
            this.loading = true
            try {
                const { data } = await seoApi.show()
                this.seo = data.data
            } finally {
                this.loading = false
            }
        },

        async saveSeo(form) {
            this.errors = {}
            try {
                const { data } = await seoApi.update(form)
                this.seo = data.data
                return true
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors
                }
                return false
            }
        },
    },
})
