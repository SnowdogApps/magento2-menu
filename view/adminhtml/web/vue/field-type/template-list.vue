<template>
    <div
        v-if="isVisible"
        class="admin__field field"
    >
        <auto-complete
            :label="label[templateType]"
            :description="description[templateType]"
            :item="item"
            :itemKey="itemKey"
            :options="options"
            :defaultOptionValue="defaultOptionValue"
            :config="config"
        >
        </auto-complete>
    </div>
</template>
<script>
define(['Vue', 'mage/translate'], function(Vue, $t) {
    Vue.component("template-list", {
        template: template,
        props: ['config', 'item', 'itemKey', 'templateType', 'typeId'],
        data: function () {
            return {
                label: {
                  'node': $t('Node template'),
                  'submenu': $t('Submenu template')
                },
                description: {
                  'node': $t('Selected template'),
                  'submenu': $t('Selected template')
                }
            }
        },
        computed: {
            templateData () {
                return this.config.fieldData[this.item.type][this.typeId];
            },
            defaultOptionValue () {
                if (this.templateData && this.templateData.defaultTemplate) {
                   return this.templateData.defaultTemplate;
                }
                return '';
            },
            options () {
                var list = [];
                if (this.templateData && this.templateData.options.length > 0) {
                    this.templateData.options.forEach(function (item, i) {
                        list.push({
                            label: item['label'] ? item['label'] : item['id'],
                            value: item['id']
                        });
                    });
                }
                return list;
            },
            isVisible () {
                return this.options.length > 0
            }
        }
    });
});
</script>
