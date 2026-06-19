import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import Vue3ApexCharts from 'vue3-apexcharts'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(Vue3ApexCharts)
app.mount('#app')
