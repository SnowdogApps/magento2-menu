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
                    :value="item.type"
                    :options="options"
                    :placeholder="config.translation.selectNodeType"
                    :get-option-label="getOptionLabel"
                    :clearable="false"
                    @input="changeType"
                />
            </div>
        </div>

        <component
            :is="item['type']"
            :item="item"
            :config="config"
        />

        <simple-field
            id="node_name"
            v-model="item.title"
            :label="config.translation.nodeName"
            type="textarea"
        />

        <simple-field
            id="node_classes"
            v-model="item.classes"
            :label="config.translation.nodeClasses"
            type="text"
        />

        <image-upload
            v-if="showImage"
            id="image"
            :item="item"
            :labels="fileUploadLabels"
        />
    </fieldset>
</template>

<script>
    define(['Vue', 'mage/translate'], function(Vue, $t) {
        Vue.component('snowdog-menu-type', {
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
            data: function() {
                return {
                    draft: {},
                    isNodeActiveLabel: $t('Enabled'),
                    fileUploadLabels: {
                        field: $t('Image'),
                        uploadAction: $t('Choose image'),
                        cancelAction: $t('Cancel'),
                        saveAction: $t('Save')
                    }
                }
            },
            computed: {
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
                showImage: function() {
                    return this.item.type !== 'cms_page'
                        && this.item.type !== 'wrapper';
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
            },
            template: template
        });
    });
</script>
