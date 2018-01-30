<template>
    <div class="admin__field field">
        <auto-complete label="Page"
                       description="Page URL Key"
                       :item="item"
                       :options="config.fieldData['cms_page'].snowMenuAutoCompleteField.options"
        >
        </auto-complete>
    </div>
</template>
<script>
    define(["Vue"], function (Vue) {
        Vue.component("cms_page", {
            template: template,
            props: ['config', 'item'],
        });
    });
</script>
