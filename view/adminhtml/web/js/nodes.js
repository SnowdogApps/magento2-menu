define([
    "Vue",
    "Vddl",
    "vue-select",
    "vue-treeselect",
    'uiRegistry',
    "vue!Snowdog_Menu/vue/app",
    "vue!Snowdog_Menu/vue/field-type/autocomplete",
    "vue!Snowdog_Menu/vue/field-type/autocomplete-lazy",
    "vue!Snowdog_Menu/vue/field-type/checkbox",
    "vue!Snowdog_Menu/vue/field-type/image-upload",
    "vue!Snowdog_Menu/vue/field-type/simple-field",
    "vue!Snowdog_Menu/vue/field-type/template-list",
    "vue!Snowdog_Menu/vue/menu-type",
    "vue!Snowdog_Menu/vue/nested-list"
], function(Vue, Vddl, vueSelect, vueTreeselect, registry) {

    return function(config) {
        var dependencies = [];

        if (config.vueComponents && config.vueComponents.length > 0) {
            dependencies = config.vueComponents;
        }

        require(dependencies, function() {
            Vue.use(Vddl);

            Vue.component('v-select', vueSelect.VueSelect);
            Vue.component('treeselect', vueTreeselect.Treeselect);

            var app = new Vue({
                el: config.el || "#snowdog-menu",
                data: config.data
            });

            registry.set('vueApp', app);
        });
    }
});
