<template>
    <div class="admin__field field">
        <simple-field
            :label="config.translation.productSku"
            id="snowmenu_node_product"
            type="textarea"
            v-model="item.content"
        >
        </simple-field>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("product", {
        template: template,
        props: ['config', 'item']
    });
});
</script>
