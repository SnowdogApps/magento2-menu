<template>
    <fieldset class="admin__fieldset fieldset-wide">
        <checkbox
            id="is_active"
            :label="isNodeActiveLabel"
            :item="item"
            :value="item.is_active"
        />

        <div class="admin__field field field-title">
            <label
                class="label admin__field-label"
                for="node_type"
            >
                {{ config.translation.nodeType }}
            </label>

            <div class="admin__field-control control">
                <v-select
                    input-id="node_type"
                    :value="item.type"
                    :options="options"
                    :placeholder="config.translation.selectNodeType"
                    :get-option-label="getOptionLabel"
                    :clearable="false"
                    @input="changeType"
                />
            </div>
        </div>

        <h2>
            {{ additionalLabel }}
        </h2>

        <component
            :is="item['type']"
            :item="item"
            :config="config"
        />

        <simple-field
            id="node_name"
            v-model="item.title"
            :label="config.translation.nodeName"
            type="text"
        />

        <simple-field
            id="node_classes"
            v-model="item.classes"
            :label="config.translation.nodeClasses"
            type="text"
        />

        <div class="admin__field field field-title">
            <label
                class="label admin__field-label"
                for="customer_groups"
            >
                {{ config.translation.customerGroups }}
            </label>

            <div class="admin__field-control control">
                <v-select
                    input-id="customer_groups"
                    v-model="item.customer_groups"
                    :reduce="customer_group => customer_group.value"
                    :options="config.customerGroups"
                    aria-describedby="customer-groups-description"
                    clearable
                    multiple
                />
                <small
                    id="customer-groups-description"
                    class="admin__field-control__description"
                >
                    {{ config.translation.customerGroupsDescription }}
                </small>
            </div>
        </div>

        <template v-if="showImage">
            <image-upload
                id="image"
                :item="item"
            />

            <simple-field
                id="image_alt_text"
                v-model="item.image_alt_text"
                :label="config.translation.imageAltText"
                type="text"
            />

            <simple-field
                id="image_width"
                v-model="item.image_width"
                :label="config.translation.imageWidth"
                type="number"
            />

            <simple-field
                id="image_height"
                v-model="item.image_height"
                :label="config.translation.imageHeight"
                type="number"
            />
        </template>

        <h2>
            {{ templatesLabel }}
        </h2>

        <template v-if="isTemplateSectionVisible">
            <template-list
                :item="item"
                :type-id="templateList['node']"
                item-key="node_template"
                template-type="node"
                :config="config"
            />
            <template-list
                :item="item"
                :type-id="templateList['submenu']"
                template-type="submenu"
                :config="config"
                item-key="submenu_template"
            />
        </template>
        <template v-else>
            <p>{{ noTemplatesMessage }}</p>
        </template>
    </fieldset>
</template>

<script>
    define(['Vue', 'mage/translate'], function(Vue, $t) {
        Vue.component('menu-type', {
            name: 'menu-type',
            props: {
                item: {
                    type: Object,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                }
            },
            data() {
                return {
                    draft: {},
                    isNodeActiveLabel: $t('Enabled'),
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
                isTemplateSectionVisible() {
                    var nodeId = this.templateList['node'],
                        submenuId = this.templateList['submenu'],
                        typeData = this.config.fieldData[this.item['type']];

                    if (typeData[nodeId] || typeData[submenuId]) {
                        return typeData[nodeId].options.length > 0 || typeData[submenuId].options.length > 0;
                    }

                    return false;
                },
                options() {
                    var list = [];
                    for (type in this.config.nodeTypes) {
                        list.push({
                            label: this.config.nodeTypes[type],
                            value: type
                        })
                    }
                    return list;
                },
                templateOptions() {
                    return this.templateOptionsData[this.item['type']] || [];
                },
                showImage() {
                    return ['category', 'product', 'custom_url'].includes(this.item.type);
                }
            },
            methods: {
                changeType(selected) {
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
                getOptionLabel(option) {
                    if (typeof option === 'object') {
                        return option.label;
                    }
                    if (option) {
                        return this.config.nodeTypes[option];
                    }
                    return option;
                }
            },
            template: template
        });
    });
</script>
