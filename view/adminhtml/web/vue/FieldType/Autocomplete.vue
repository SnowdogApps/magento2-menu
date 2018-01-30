<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label">
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <v-select v-model="item.content"
                      :options="fieldOptions"
                      :placeholder="placeHolder"
            >
            </v-select>
        </div>
        <div class="selected-option">
            <div class="selected-option__label">
                {{ description }}
            </div>
            <div class="selected-option__value">
                <span v-if="selectedId">
                    {{selectedId}}
                </span>
                <span v-else>
                    {{ placeHolder }} ⇡
                </span>
            </div>
        </div>
    </div>
</template>
<script>
    define(["Vue"], function (Vue) {
        Vue.component("auto-complete", {
            template: template,
            props: [
                'label',
                'description',
                'options',
                'item'
            ],
            computed: {
                fieldOptions: function () {
                    return Object.keys(this.options);
                },
                selectedId() {
                    return this.item.content ? this.item.content : '';
                },
                placeHolder: function () {
                    return 'Please select a ' + this.label.toLocaleLowerCase();
                }
            }
        });
    });
</script>
