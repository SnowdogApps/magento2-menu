<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label">
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <v-select v-model="selected"
                      :options="fieldOptions"
                      :placeholder="placeHolder"
            >
            </v-select>
        </div>
        <div class="selected-option">
            <p class="selected-option__label">
                {{ description }}
            </p>
            <p class="selected-option__value">
                <span v-if="selectedId">
                    {{selectedId}}
                </span>
                <span v-else>
                    {{ placeHolder }} ⇡
                </span>
            </p>
        </div>
    </div>
</template>
<script>
    import vSelect from 'vue-select';

    export default {
        data() {
            return {
                selected: null,
                fieldOptions: Object.keys(this.options),
                placeHolder: `Please select a ${this.label.toLocaleLowerCase()}`
            }
        },
        props: [
            'label',
            'description',
            'options',
        ],
        computed: {
            selectedId() {
                return this.options[this.selected];
            }
        },
        components: {
            vSelect
        }
    }
</script>