<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label">
            {{ label }}
        </label>

        <div class="admin__field-control control">
            <treeselect
                v-if="isTree"
                v-model="selected"
                :options="optionsTree"
                :placeholder="placeholder"
                :default-expand-level="1"
                :clearable="false"
            >
                <template v-slot:value-label="{ node }">
                    {{ node.raw.full_label }}
                </template>
            </treeselect>

            <v-select
                v-else
                v-model="selected"
                :options="options"
                :placeholder="placeholder"
                :clearable="false"
            >
                <template v-slot:option="option">
                    {{ option.label }}

                    <template v-if="option.store">
                        <span class="vs__dropdown-option__details">
                            {{ option.store.join(', ') }}
                        </span>
                    </template>
                </template>
            </v-select>
        </div>
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('auto-complete', {
            props: {
                label: {
                    type: String,
                    required: true
                },
                description: {
                    type: String,
                    required: true
                },
                options: {
                    type: Array,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                },
                itemKey: {
                    type: String,
                    required: true
                },
                defaultOptionValue: {
                    type: String,
                    default: 'default'
                },
                isTree: {
                    type: Boolean,
                    default: false
                },
                optionsTree: {
                    type: Array,
                    default: () => []
                }
            },
            computed: {
                selected: {
                    get() {
                        var selectedOption = '',
                            optionValue;

                        for (var i = 0; i < this.options.length; i++) {
                            optionValue = this.options[i].value.toString();
                            if (optionValue === this.item[this.itemKey]) {
                                selectedOption = this.isTree ? this.options[i].value : this.options[i];
                            }
                        }

                        if (!selectedOption) {
                            selectedOption = this.defaultSelectedOption;
                        }

                        return selectedOption;
                    },
                    set(option) {
                        if (option && typeof option === 'object') {
                            this.item[this.itemKey] = option.value.toString();
                        }
                        else if (option && typeof option === 'string') {
                            this.item[this.itemKey] = option;
                        }
                        else {
                          this.item[this.itemKey] = this.defaultSelectedOption ? this.defaultSelectedOption.value.toString() : '';
                        }
                    }
                },
                placeholder: function() {
                    return this.config.translation.pleaseSelect + ' ' + this.label.toLocaleLowerCase();
                }
            },
            created() {
                var optionValue;

                for (var i = 0; i < this.options.length; i++) {
                    optionValue = this.options[i].value.toString();
                    if (optionValue === this.defaultOptionValue) {
                        this.defaultSelectedOption = this.options[i];
                    }
                }
            },
            template: template
        });
    });
</script>
