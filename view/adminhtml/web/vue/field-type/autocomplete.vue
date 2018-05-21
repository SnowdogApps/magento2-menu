<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label">
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <v-select
                v-model="selected"
                :options="options"
                :placeholder="placeHolder"
            >
            </v-select>
        </div>
        <div class="selected-option">
            <div class="selected-option__label">
                {{ description }}
            </div>
            <div class="selected-option__value">
                <span v-if="item.content">
                    {{ item.content }}
                </span>
                <span v-else>
                    {{ placeHolder }} ⇡
                </span>
            </div>
        </div>
    </div>
</template>
<script>
define(["Vue"], function(Vue) {
    Vue.component("auto-complete", {
        template: template,
        props: [
            'label',
            'description',
            'options',
            'item',
            'config'
        ],
        computed: {
            selected: {
                get() {
                    selectedOption = '';
                    for (var i = 0; i < this.options.length; i++) {
                        if (this.options[i].value.toString() === this.item.content) {
                            selectedOption = this.options[i];
                        }
                    }
                    return selectedOption;
                },
                set(option) {
                    if (typeof option === 'object') {
                        this.item.content = option.value.toString();
                    }
                }
            },
            placeHolder: function() {
                return this.config.translation.pleaseSelect + " " + this.label.toLocaleLowerCase();
            }
        }
    });
});
</script>
