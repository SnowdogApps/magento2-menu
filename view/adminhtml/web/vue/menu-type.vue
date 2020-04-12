<template>
    <fieldset class="admin__fieldset fieldset-wide">
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
                    :searchable="false"
                    @input="changeType"
                />
            </div>
        </div>

        <component
            :is="item['type']"
            :item="item"
            :config="config"
        />
    </fieldset>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('snowdog-menu-type', {
            props: ['item', 'config'],
            data: function() {
                return {
                    draft: {},
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
