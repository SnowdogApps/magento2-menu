<template>
    <autocomplete
        :label="config.translation.block"
        :description="config.translation.blockId"
        :item="item"
        item-key="content"
        :options="config.fieldData.cms_block.snowMenuAutoCompleteField.options"
        :config="config"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('cms_block', {
            name: 'cms-block',
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
