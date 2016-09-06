define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function ($) {
    return function(options, element) {
        var buttonContainer = $(element);
        var treeContainer = $('#snowmenu_tree_container');
        var tree = treeContainer.jstree(true);
        buttonContainer.children('button').click(function(e) {
            e.preventDefault();
            var data = {
                data: {
                    type: $(this).data('type')
                }
            };
            var nodeId = tree.create_node(tree.get_selected(), data);
            tree.deselect_node(tree.get_selected());
            tree.select_node(nodeId);
        });
    }
});