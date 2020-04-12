<template>
    <div class="admin__field field">
        <simple-field
            id="snowmenu_node_product"
            v-model="item.content"
            :label="config.translation.productId"
            type="textarea"
        />
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('product', {
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
