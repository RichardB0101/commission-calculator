import './bootstrap';
import { createApp } from 'vue';
import CommissionCalculator from './components/pages/calculator/Index.vue';
import { i18nVue } from 'laravel-vue-i18n'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
})

createApp({})
    .use(i18nVue, {
        lang: 'pt',
        resolve: lang => {
            const langs = import.meta.glob('../../lang/*.json', { eager: true });
            return langs[`../../lang/${lang}.json`].default;
        },
    })
    .use(vuetify)
    .component('commission-calculator', CommissionCalculator)
    .mount('#app')
