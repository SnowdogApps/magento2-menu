<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label"
               :for="fieldId"
        >
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <textarea class="input-text admin__control-text"
                      :id="fieldId"
                      :type="type"
                      v-model="value"
            />
        </div>
    </div>
</template>
<script>

    export default {
        props: ['label', 'id', 'code', 'type', 'value'],
        data() {
            return {
                fieldId: `snowmenu_${this.id}`
            }
        }
    }
</script>