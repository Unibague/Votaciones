require('./bootstrap');

// Import modules...
import Vue from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';
import PortalVue from 'portal-vue';
//Vuetify config
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import es from 'vuetify/es5/locale/es'; // 👈 Importa el idioma español
Vue.use(Vuetify)

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);
Vue.use(PortalVue);


const app = document.getElementById('app');

// 👇 Aquí defines la instancia de Vuetify con el idioma
const vuetify = new Vuetify({
    lang: {
        locales: { es },
        current: 'es',
    },
});

new Vue({
    vuetify, // 👈 Pasa la instancia personalizada aquí
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
