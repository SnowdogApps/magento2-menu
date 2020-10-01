define([
    "Vue",
    "Vddl",
    "vue-select",
    "vue!Snowdog_Menu/vue/app",
    "vue!Snowdog_Menu/vue/nested-list",
    "vue!Snowdog_Menu/vue/menu-type",
    "vue!Snowdog_Menu/vue/menu-type/category",
    "vue!Snowdog_Menu/vue/menu-type/cms-page",
    "vue!Snowdog_Menu/vue/menu-type/cms-block",
    "vue!Snowdog_Menu/vue/menu-type/custom-url",
    "vue!Snowdog_Menu/vue/menu-type/product",
    "vue!Snowdog_Menu/vue/menu-type/wrapper",
    "vue!Snowdog_Menu/vue/field-type/simple",
    "vue!Snowdog_Menu/vue/field-type/checkbox",
    "vue!Snowdog_Menu/vue/field-type/autocomplete"
], function(Vue, Vddl, vueSelect) {
    return function(config) {
        Vue.use(Vddl);
        Vue.component('v-select', vueSelect.VueSelect);
        var app = new Vue({
            el  : config.el || "#snowdog_menu",
            data: config.data
        });
    }
});
