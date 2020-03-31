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
                <span v-if="item[itemKey]">
                    {{ item[itemKey] }}
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
        props: {
            label: {
                type: String
            },
            description: {
                type: String
            },
            options: {
                type: Array
            },
            item: {
                type: Object
            },
            config: {
                type: Object
              },
            itemKey: {
                type: String,
                default: 'content'
            },
            defaultOptionValue: {
                type: String,
                default: 'default'
            }
        },
        data () {
          return {
              defaultSelectedOption: false
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
                            selectedOption = this.options[i];
                        }
                        if (optionValue === this.defaultOptionValue) {
                            this.defaultSelectedOption = this.options[i];
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
                    else {
                      this.item[this.itemKey] = this.defaultSelectedOption ? this.defaultSelectedOption.value.toString() : '';
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
