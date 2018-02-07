define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function(options, element) {
        var removeButton  = $(element),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        removeButton.click(function(e) {
            var selected = tree.get_selected();

            if (selected.length === 0) {
                selected = '#';
            }

            if (selected !== '#') {
                tree.delete_node(selected);
            }
        });
    }
});
