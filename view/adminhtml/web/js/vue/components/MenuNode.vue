<template>
    <div class="admin__field">
        <textarea-field label="Node name"
                        id="node_name_custom_url"
                        code="node_name"
                        type="textarea"
                        value="New node"

        >
        </textarea-field>
        <simple-field label="Node CSS classes"
                      id="node_classes_custom_url"
                      code="node_classes"
                      type="text"
        >
        </simple-field>
        <component :is="type" :fieldData="fieldData"></component>
    </div>
</template>

<script>
    import Category from './menu_type/Category.vue'
    import CmsPage from './menu_type/CmsPage.vue'
    import CmsBlock from './menu_type/CmsBlock.vue'
    import CustomUrl from './menu_type/CustomUrl.vue'
    import SimpleField from './fields/Simple.vue'
    import textareaField from './fields/Textarea.vue'

    export default {
        props: ['type', 'fieldData'],
        components: {
            Category,
            CmsPage,
            CmsBlock,
            CustomUrl,
            SimpleField,
            textareaField,
        }
    }
</script>