<template>
    <div id="snowdog-menu">
        <toolbar></toolbar>
        <MenuNode v-if="nodeType" :type="nodeType" :fieldData="FieldData"></MenuNode>
    </div>
</template>

<script>
    import {FieldsData} from './data';
    import Toolbar from './components/Toolbar.vue'
    import MenuNode from './components/MenuNode.vue'

    export default {
        name: 'app',
        components: {
            Toolbar,
            MenuNode,
        },
        data() {
            return {
                nodeType: null,
                FieldData: null,
            }
        },
        events: {
            'node-type-set'(nodeType) {
                this.FieldData = FieldsData[nodeType];
                this.nodeType = nodeType;
            }
        }
    }
</script>