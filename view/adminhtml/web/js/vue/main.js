import Vue from 'vue'
import App from './App.vue'
import VueEvents from 'vue-events'

Vue.use(VueEvents);

new Vue({
    el: '#snowdog-menu',
    render: h => h(App)
});
