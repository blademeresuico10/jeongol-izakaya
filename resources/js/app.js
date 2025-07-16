import './bootstrap';
import { createApp } from 'vue';
import reservations from './components/reservations.vue';

const app = createApp({});
app.component('reservations-view', reservations);
app.mount('#app');

