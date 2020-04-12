<template>
    <div class="admin__field field">
        <auto-complete
            :label="config.translation.block"
            :description="config.translation.blockId"
            :item="item"
            :options="config.fieldData.cms_block.snowMenuAutoCompleteField.options"
            :config="config"
        />
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('cms_block', {
            props: ['config', 'item'],
            template: template
        });
    });
</script>
