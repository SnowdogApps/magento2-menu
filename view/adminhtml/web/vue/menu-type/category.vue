<template>
    <auto-complete
        :is-tree="true"
        :label="config.translation.category"
        :description="config.translation.categoryId"
        :item="item"
        :item-key="'content'"
        :options="config.fieldData.category.snowMenuAutoCompleteField.options"
        :config="config"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('category', {
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
