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
                :disabled="isDisabled"
            >
                <template v-slot:option="option">
                    {{ option.label }}

                    <template v-if="option.store && option.store.length">
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
        Vue.component('autocomplete', {
            name: 'autocomplete',
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
                itemIdKey: {
                    type: String,
                    default: null
                },
                defaultOptionValue: {
                    type: String,
                    default: 'default'
                },
                isTree: {
                    type: Boolean,
                    default: false
                },
                isDisabled: {
                    type: Boolean,
                    default: false
                }
            },
            computed: {
                selected: {
                    get() {
                        let selectedOption = '',
                            optionValue;
                        const selectedItemId = this.item[this.itemIdKey];

                        if (selectedItemId) {
                            for (var i = 0; i < this.options.length; i++) {
                                optionId = this.options[i].id.toString();
                                if (optionId === selectedItemId) {
                                    selectedOption = this.isTree ? this.options[i].value : this.options[i];
                                }
                            }
                        } else {
                            for (var i = 0; i < this.options.length; i++) {
                                optionValue = this.options[i].value.toString();
                                if (optionValue === this.item[this.itemKey]) {
                                    selectedOption = this.isTree ? this.options[i].value : this.options[i];
                                }
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
                            this.item[this.itemIdKey] = option.id.toString();
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
                },
                optionsTree: function() {
                    const hashTable = {},
                            optionsTree = [];

                    this.options.forEach(item => hashTable[item.id] = {...item});
                    this.options.forEach(item => {
                        if (item.parent_id && hashTable[item.parent_id]) {
                            hashTable[item.parent_id].children = [
                                ...(hashTable[item.parent_id].children || []),
                                hashTable[item.id]
                            ];
                        } else {
                            optionsTree.push(hashTable[item.id]);
                        }
                    });

                    return optionsTree;
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
