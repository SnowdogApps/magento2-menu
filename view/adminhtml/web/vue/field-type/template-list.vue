<template>
    <div
        v-if="isVisible"
        class="admin__fieldset fieldset-wide"
    >
        <auto-complete
            :label="label[templateType]"
            :description="description[templateType]"
            :item="item"
            :item-key="itemKey"
            :options="options"
            :default-option-value="defaultOptionValue"
            :config="config"
        />
    </div>
    <div
        v-else
    >
        {{ noCustomTemplatesMessage[templateType] }}
    </div>
</template>
<script>
define(['Vue', 'mage/translate'], function(Vue, $t) {
    Vue.component("template-list", {
        props: {
          config: {
            type: Object,
            required: true
          },
          item: {
            type: Object,
            required: true
          },
          itemKey: {
            type: String,
            required: true
          },
          templateType: {
            type: String,
            required: true
          },
          typeId: {
            type: String,
            required: true
          }
        },
        data: function () {
            return {
                label: {
                  'node': $t('Node template'),
                  'submenu': $t('Submenu template')
                },
                description: {
                  'node': $t('Selected template'),
                  'submenu': $t('Selected template')
                },
                noCustomTemplatesMessage: {
                  'node': $t('There is no custom node template for this type of node.'),
                  'submenu': $t('There is no custom submenu (children wrapper) template for this type of node.')
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
                return this.options.length > 1
            }
        },
        template: template
    });
});
</script>
