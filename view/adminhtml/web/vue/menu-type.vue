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
                for="node_type"
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
        <component :is="item['type']" :item="item" :config="config"></component>
    </fieldset>
</template>

<script>
define(["Vue"], function(Vue) {
    Vue.component("snowdog-menu-type", {
        template: template,
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
        }
    });
});
</script>
