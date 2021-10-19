<template>
    <div class="admin__fieldset fieldset-wide">
        <autocomplete
            :label="label[templateType]"
            :description="description[templateType]"
            :item="item"
            :item-key="itemKey"
            :options="options"
            :default-option-value="defaultOptionValue"
            :config="config"
            :is-disabled="isDisabled"
        />
    </div>
</template>
<script>
define(['Vue', 'mage/translate'], function(Vue, $t) {
    Vue.component("template-list", {
        name: 'template-list',
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
            isDisabled () {
                return this.options.length <= 1
            }
        },
        template: template
    });
});
</script>
