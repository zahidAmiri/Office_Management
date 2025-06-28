import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js'; // ✅ Correct
import { createI18n } from 'vue-i18n'; // ✅ 1. Import i18n
import messages from './lang';    




const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


const i18n = createI18n({
    legacy: false, // ✅ required for Composition API
    globalInjection: true, // ✅ allows using $t globally in templates
    locale: localStorage.getItem('locale') || 'en',
    fallbackLocale: 'en',
    messages,
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
