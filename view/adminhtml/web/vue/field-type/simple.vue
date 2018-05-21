<template>
    <div class="admin__field field field-title">
        <label
            class="label admin__field-label"
            :for="fieldId"
        >
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <input
                class="input-text admin__control-text"
                :id="fieldId"
                :type="type"
                :value="value"
                @input="updateValue($event.target.value)"
            />
        </div>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("simple-field", {
        template: template,
        props: ['label', 'id', 'type', 'value'],
        data: function() {
            return {
                fieldId: ''
            }
        },
        methods: {
            updateValue: function(value) {
                this.$emit('input', value);
            }
        },
        mounted: function() {
            this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
        }
    });
});
</script>
