import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';

// Configure axios for CSRF
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add request interceptor to get fresh CSRF token on every request
axios.interceptors.request.use((config) => {
    const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
    }
    return config;
});

// Add response interceptor to handle CSRF token mismatch
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 419) {
            // CSRF token mismatch - try to get a fresh token and retry the request
            console.warn('CSRF token mismatch detected, fetching new token...');
            
            try {
                // Get fresh CSRF token
                const tokenResponse = await axios.get('/csrf-token');
                const newToken = tokenResponse.data.token;
                
                // Update the meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
                if (metaToken) {
                    metaToken.content = newToken;
                }
                
                // Retry the original request with new token
                error.config.headers['X-CSRF-TOKEN'] = newToken;
                return axios.request(error.config);
            } catch (tokenError) {
                // If we can't get a new token, refresh the page
                console.warn('Failed to get new CSRF token, refreshing page...');
                window.location.reload();
            }
        }
        return Promise.reject(error);
    }
);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
