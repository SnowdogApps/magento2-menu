var config = {
    paths: {
        'menuNodes': 'Snowdog_Menu/js/nodes',
        'Vue': 'Snowdog_Menu/js/lib/vue',
        'vue': 'Snowdog_Menu/js/lib/require-vuejs',
        'Vddl': 'Snowdog_Menu/js/lib/vddl',
        'vue-select': 'Snowdog_Menu/js/lib/vue-select',
        'vue-treeselect': 'Snowdog_Menu/js/lib/vue-treeselect',
        'uuid': 'Snowdog_Menu/js/lib/uuidv4.min'
    },
    shim: {
        'Vue': { 'exports': 'Vue' }
    },
    config: {
        mixins: {
          "Magento_Ui/js/modal/modal-component": {
            "Snowdog_Menu/js/mixins/modal-mixin": true
          }
        }
    },
};
