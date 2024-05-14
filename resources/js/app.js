import './bootstrap';
import { createApp } from 'vue';
import CommissionCalculator from './components/pages/calculator/Index.vue';

createApp({})
    .component('commission-calculator', CommissionCalculator)
    .mount('#app')
