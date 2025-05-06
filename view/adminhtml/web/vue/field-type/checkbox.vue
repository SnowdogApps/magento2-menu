<template>
    <div
        class="
            admin__field
            field
            field-title
            admin__scope-old
        "
    >
        <label
            class="label admin__field-label"
            :for="fieldId"
        >
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <div
                class="admin__actions-switch"
                data-role="switcher"
            >
                <input
                    :id="fieldId"
                    v-model="checkboxValue"
                    :value="checkboxValue"
                    type="checkbox"
                    class="admin__actions-switch-checkbox"
                >
                <label
                    class="admin__actions-switch-label"
                    :for="fieldId"
                >
                    <span
                        class="admin__actions-switch-text"
                        data-bind="attr: {
                            'data-text-on': toggleLabels.on,
                            'data-text-off': toggleLabels.off
                        }"
                        data-text-on="Yes"
                        data-text-off="No"
                    />
                </label>
            </div>
            <small
                v-if="description"
                class="admin__field-control__description"
            >
                {{ description }}
            </small>
        </div>
    </div>
</template>

<script>
define(["Vue"], function(Vue) {
    Vue.component("checkbox", {
        name: 'checkbox',
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
            },
            description: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                fieldId: '',
                checkboxValue: Number(this.value) === 1 ? true : false
            }
        },
        watch: {
            checkboxValue(newValue) {
                this.item[this.id] = newValue ? '1' : '0';
            }
        },
        mounted() {
            this.fieldId = 'snowmenu_' + this.id + '_' + this._uid;
        },
        template: template
   });
});
</script>
