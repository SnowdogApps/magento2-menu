define([
    "Vue",
    "Vddl",
    "vue-select",
    "vue-treeselect",
    "vue!Snowdog_Menu/vue/app",
    "vue!Snowdog_Menu/vue/field-type/autocomplete",
    "vue!Snowdog_Menu/vue/field-type/checkbox",
    "vue!Snowdog_Menu/vue/field-type/image-upload",
    "vue!Snowdog_Menu/vue/field-type/simple",
    "vue!Snowdog_Menu/vue/field-type/template-list",
    "vue!Snowdog_Menu/vue/menu-type",
    "vue!Snowdog_Menu/vue/nested-list"
], function(Vue, Vddl, vueSelect, vueTreeselect) {

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
                el  : config.el || "#snowdog_menu",
                data: config.data
            });
        });
    }
});
