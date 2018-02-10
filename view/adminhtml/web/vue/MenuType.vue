<template>
    <fieldset class="admin__fieldset fieldset-wide">
        <simple-field label="Node name"
                      id="node_name"
                      type="textarea"
                      v-model="item.title"

        >
        </simple-field>
        <simple-field label="Node CSS classes"
                      id="node_classes"
                      type="text"
                      v-model="item.classes"
        >
        </simple-field>
        <div class="admin__field field field-title">
            <label class="label admin__field-label"
                   for="node_type"
            >
                {{ config.translation.nodeType }}
            </label>
            <div class="admin__field-control control">
                <select class="admin__control-select"
                        name="node_type"
                        id="node_type"
                        :value="item.type"
                        @change="changeType($event.target.value)"
                >
                    <option value="">{{config.translation.selectNodeType}}</option>
                    <option v-for="(label, key) in config.nodeTypes" :value="key">
                        {{ label }}
                    </option>
                </select>
            </div>
        </div>
        <component :is="item['type']" :item="item" :config="config"></component>
    </fieldset>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("snowdog-menu-type", {
            template: template,
            props: ['item', 'config'],
            methods: {
                changeType: function (value) {
                    this.item['type'] = value;
                    this.item['content'] = null;
                }
            }
        });
    });
</script>
