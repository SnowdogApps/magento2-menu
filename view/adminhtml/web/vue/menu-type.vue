<template>
    <fieldset class="admin__fieldset fieldset-wide">
        <simple-field
            :label="config.translation.nodeName"
            id="node_name"
            type="textarea"
            v-model="item.title"
        >
        </simple-field>
        <simple-field
            :label="config.translation.nodeClasses"
            id="node_classes"
            type="text"
            v-model="item.classes"
        >
        </simple-field>
        <div class="admin__field field field-title">
            <label
                class="label admin__field-label"
                for="node_template"
            >
                {{ config.translation.nodeType }}
            </label>
            <div class="admin__field-control control">
                <v-select
                    :value="item.type"
                    @input="changeType"
                    :options="options"
                    :placeholder="config.translation.selectNodeType"
                    :getOptionLabel="getOptionLabel"
                    :searchable="false"
                >
                </v-select>
            </div>
        </div>
        <h2>
            {{ additionalLabel }}
        </h2>
        <component :is="item['type']" :item="item" :config="config"></component>
        <h2>
            {{ templatesLabel }}
        </h2>
        <template v-if="isTemplateSectionVisible">
            <component
                is="template-list"
                :item="item"
                :typeId="templateList['node']"
                itemKey="template"
                templateType="node"
                :config="config"
            ></component>
            <component
                is="template-list"
                :item="item"
                :typeId="templateList['submenu']"
                templateType="submenu"
                :config="config"
                itemKey="submenuTemplate"
            ></component>
        </template>
        <template v-else>
            <p>{{ noTemplatesMessage }}</p>
        </template>
    </fieldset>
</template>

<script>
define(["Vue", "mage/translate"], function(Vue, $t) {
    Vue.component("snowdog-menu-type", {
        template: template,
        props: ['item', 'config'],
        data: function() {
            return {
                draft: {},
                additionalLabel: $t('Additional type options'),
                noTemplatesMessage: $t('There is no custom defined templates defined in theme for this node type'),
                templatesLabel: $t('Templates'),
                templateList: {
                  'node': 'snowMenuNodeCustomTemplates',
                  'submenu': 'snowMenuSubmenuCustomTemplates',
                }
            }
        },
        computed: {
            isTemplateSectionVisible: function() {
                var nodeId = this.templateList['node'],
                    submenuId = this.templateList['submenu'],
                    typeData = this.config.fieldData[this.item['type']];

                if (typeData[nodeId] || typeData[submenuId]) {
                    return typeData[nodeId].options.length > 0 || typeData[submenuId].options.length > 0;
                }

                return false;
            },
            options: function() {
                var list = [];
                for (type in this.config.nodeTypes) {
                    list.push({
                        label: this.config.nodeTypes[type],
                        value: type
                    })
                }
                return list;
            },
            templateOptions: function() {
                return this.templateOptionsData[this.item['type']] || [];
            }
        },
        methods: {
            changeType: function(selected) {
                if (selected && typeof selected === 'object') {
                    var type  = this.item.type,
                        value = selected.value;
                    if (type) {
                        this.draft[type] = {
                            content: this.item['content']
                        };
                    }
                    if (this.draft[value]) {
                        this.item['content'] = this.draft[value].content;
                    } else {
                        this.item['content'] = null;
                    }
                    this.item['type'] = value;
                }
            },
            getOptionLabel: function(option) {
                if (typeof option === 'object') {
                    return option.label;
                }
                if (option) {
                    return this.config.nodeTypes[option];
                }
                return option;
            }
        }
    });
});
</script>
