<template>
    <div class="admin__field field">
        <auto-complete label="Block"
                       description="Block ID"
                       :item="item"
                       :options="config.fieldData['cms_block'].snowMenuAutoCompleteField.options"
        >
        </auto-complete>
    </div>
</template>
<script>
    define(["Vue"], function (Vue) {
        Vue.component("cms_block", {
            template: template,
            props: ['config', 'item']
        });
    });
</script>
