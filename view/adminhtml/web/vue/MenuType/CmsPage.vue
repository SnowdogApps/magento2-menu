<template>
    <div class="admin__field field">
        <auto-complete
            :label="config.translation.page"
            :description="config.translation.pageId"
            :item="item"
            :options="config.fieldData['cms_page'].snowMenuAutoCompleteField.options"
            :config="config"
        >
        </auto-complete>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("cms_page", {
        template: template,
        props: ['config', 'item'],
    });
});
</script>
