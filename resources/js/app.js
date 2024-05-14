import './bootstrap';
import { createApp } from 'vue';
import CommissionCalculator from './components/pages/calculator/Index.vue';
import { i18nVue } from 'laravel-vue-i18n'

createApp({})
    .use(i18nVue, {
        lang: 'pt',
        resolve: lang => {
            const langs = import.meta.glob('../../lang/*.json', { eager: true });
            return langs[`../../lang/${lang}.json`].default;
        },
    })
    .component('commission-calculator', CommissionCalculator)
    .mount('#app')
