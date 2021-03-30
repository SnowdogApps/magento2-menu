<template>
    <auto-complete
        :is-tree="true"
        :label="config.translation.categoryChild"
        :description="config.translation.categoryId"
        :item="item"
        :item-key="'content'"
        :options="config.fieldData.category_child.snowMenuAutoCompleteField.options"
        :config="config"
    />
</template>
<script>
    define(["Vue"], function(Vue) {
        Vue.component('category_child', {
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
            template: template
        });
    });
</script>
