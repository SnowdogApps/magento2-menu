<template>
    <div class="admin__field field">
        <auto-complete
            :label="config.translation.category"
            :description="config.translation.categoryId"
            :item="item"
            :options="config.fieldData.category.snowMenuAutoCompleteField.options"
            :config="config"
        >
        </auto-complete>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("category", {
        template: template,
        props: ['config', 'item']
    });
});
</script>
