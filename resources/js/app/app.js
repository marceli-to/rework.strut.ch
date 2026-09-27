import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { useToast } from './composables/useToast'

const app = createApp(App)

app.use(createPinia())
app.use(router)

router.afterEach(() => {
	useToast().clearErrors()
})

app.mount('#app')
