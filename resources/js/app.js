import './bootstrap';

import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialize AOS
AOS.init();
import './libs';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'
import { InertiaProgress } from '@inertiajs/progress'

// Auto-register Vue pages with Vite
const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
const el = document.getElementById('app')

InertiaProgress.init({
  delay: 250,        // only show if request > 250ms (to avoid flicker)
  color: '#29d',     // default color, you can change it
  includeCSS: true,  // injects default styling
  showSpinner: false // no spinner
})

if(el?.dataset?.page){
  createInertiaApp({
  resolve: name => pages[`./Pages/${name}.vue`],
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    app.use(ZiggyVue,Ziggy)
    app.mount(el)
  },
})
}
