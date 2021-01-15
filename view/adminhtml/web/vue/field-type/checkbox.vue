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
                :id="fieldId"
                v-model="checkboxValue"
                class="input-text admin__control-text"
                type="checkbox"
            >
        </div>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("checkbox", {
        props: {
            label: {
                type: String,
                required: true
            },
            id: {
                type: String,
                required: true
            },
            value: {
                type: [Number, String],
                required: true
            },
            item: {
                type: Object,
                required: true
            }
        },
        data: function() {
            return {
                fieldId: '',
                checkboxValue: this.value === '1' ? true : false
            }
        },
        watch: {
            checkboxValue: function(newValue) {
                this.item.is_active = newValue ? '1' : '0';
            }
        },
        mounted: function() {
            this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
        },
        template: template
   });
});
</script>
