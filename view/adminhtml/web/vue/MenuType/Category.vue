<template>
    <div class="admin__field field">
        <auto-complete label="Category"
                       description="Category ID"
                       :item="item"
                       :options="config.fieldData.category.snowMenuAutoCompleteField.options"
        >
        </auto-complete>
    </div>
</template>
<script>
    define(["Vue"], function (Vue) {
        Vue.component("category", {
            template: template,
            props: ['config', 'item']
        });
    });
</script>
