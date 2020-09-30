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
                type="checkbox"
                v-model="checkboxValue"
            />
        </div>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("checkbox", {
        template: template,
        props: ['label', 'id', 'value', 'item'],
        data: function() {
            return {
                fieldId: '',
                checkboxValue: this.value === '1' ? true : false
            }
        },
        mounted: function() {
            this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
        },
        watch: {
          checkboxValue: function(newValue) {
            this.item.is_active = newValue
          }
        }
   });
});
</script>
