<template>
    <autocomplete
        :label="config.translation.page"
        :description="config.translation.pageId"
        :item="item"
        item-key="content"
        item-id-key="selected_item_id"
        :options="config.fieldData.cms_page.snowMenuAutoCompleteField.options"
        :config="config"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('cms_page', {
            name: 'cms-page',
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
