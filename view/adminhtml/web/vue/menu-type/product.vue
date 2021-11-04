<template>
    <simple-field
        id="snowmenu_node_product"
        v-model="item.content"
        :label="config.translation.productId"
        type="textarea"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('product', {
            name: 'product',
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
