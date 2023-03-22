import './bootstrap';
import '../css/app.scss';

import 'moment/dist/locale/pt-br';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';

// vuetify
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import {pt} from 'vuetify/locale'
import {md3} from 'vuetify/blueprints'
import 'vuetify/dist/vuetify.min.css'

const vuetify = createVuetify({
    components,
    directives,
    locale: {
        locale: 'pt',
        messages: {pt}
    },
    styles: {
        configFile: '../css/app.scss'
    },
    blueprint: md3,
    defaults: {
        VTextField: {
            variant: 'solo',
        },
        VTextArea: {
            variant: 'solo'
        },
        VAutocomplete: {
            variant: 'solo'
        }
    }
})


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        return createApp({
            render: () => h(App, props),
            vuetify: vuetify,
        })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(vuetify)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    }
});
