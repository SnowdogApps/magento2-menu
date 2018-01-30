<template>
    <div class="admin__field">
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
                Node Type
            </label>
            <div class="admin__field-control control">
                <select class="admin__control-select"
                        name="node_type"
                        id="node_type"
                        :value="item['data-type']"
                        @change="changeType($event.target.value)"
                >
                    <option value="">Select Node Type</option>
                    <option v-for="(label, key) in config.nodeTypes" :value="key">
                        {{label}}
                    </option>
                </select>
            </div>
        </div>
        <component :is="item['data-type']" :item="item" :config="config"></component>
        <button @click.prevent="removeEvent">Remove</button>
    </div>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("snowdog-menu-type", {
            template: template,
            props: ['item', 'config'],
            methods: {
                changeType: function (value) {
                    this.item['data-type'] = value;
                    this.item['content'] = null;
                },
                removeEvent: function () {
                    this.$emit('removeEvent', this.item)
                }
            }
        });
    });
</script>
