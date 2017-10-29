<template>
    <div class="admin__field field">
        <auto-complete label="Category"
                       description="Category ID"
                       :options="fieldData.snowMenuAutoCompleteField.options"
        >
        </auto-complete>
    </div>
</template>
<script>
    import AutoComplete from '../fields/Autocomplete.vue'

    export default {
        props: ['fieldData'],
        components: {
            AutoComplete
        }
    }
</script>