<template>
    <form class="admin__field">
        <button @click.prevent="setNodeType('Category')">
            Add Category node
        </button>
        <button @click.prevent="setNodeType('CmsPage')">
            Add Cms Page link node
        </button>
        <button @click.prevent="setNodeType('CmsBlock')">
            Add Cms Block node
        </button>
        <button @click.prevent="setNodeType('CustomUrl')">
            Add Custom Url node
        </button>
    </form>
</template>
<script>
    export default {
        data() {
            return {}
        },
        methods: {
            setNodeType(nodeType) {
                this.$events.fire('node-type-set', nodeType);
            }
        }
    }
</script>