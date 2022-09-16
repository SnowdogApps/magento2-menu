<template>
    <component
        :is="component"
        :is-tree="true"
        :label="config.translation.categoryChild"
        :description="config.translation.categoryId"
        :item="item"
        :item-key="'content'"
        :options="options"
        :config="config"
    />
</template>
<script>
    define(["Vue"], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('category_child', {
            name: 'category-child',
            props: {
                config: {
                    type: Object,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                }
            },
            computed: {
                options() {
                    return this.config.fieldData.category.snowMenuAutoCompleteField.options;
                },
                component() {
                    return this.options.length > this.config.lazyMinItemsCount
                    ? 'autocomplete-lazy'
                    : 'autocomplete';
                }
            },
            template: template
        });
    });
</script>
