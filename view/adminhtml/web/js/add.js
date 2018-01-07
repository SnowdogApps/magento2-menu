define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function(options, element) {
        var container     = $(element),
            treeContainer = $('#snowmenu_tree_container'),
            addButton     = container.find('button'),
            tree          = treeContainer.jstree(true),
            selectField   = container.find('select'),
            nodeType      = '';


        selectField.change(function(e) {
            nodeType = this.value;
            addButton.attr('disabled', this.value == '')
        });

        addButton.click(function(e) {
            var selected = tree.get_selected();

            if (selected.length === 0) {
                selected = '#';
            }

            var data   = {
                    data: {
                        type: nodeType
                    }
                },
                nodeId = tree.create_node(selected, data);

            tree.deselect_node(selected);
            tree.select_node(nodeId);
        });
    }
});
