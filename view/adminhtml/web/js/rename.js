define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit',
    'snowWysiwygSetup'
], function($) {
    return function(options, element) {
        var nodeInput     = $(element),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        treeContainer.on("changed.jstree", function(e, data) {
            if (options.type === 'node_classes') {
                var node = data.instance.get_node(data.selected);

                if (node.data) {
                    nodeInput.val(node.data.classes);
                }
            }
        });

        nodeInput.change(function() {
            if (options.type === 'node_name') {
                tree.rename_node(tree.get_selected(), $(this).val());
            }
            else if (options.type === 'node_classes') {
                tree.get_node(tree.get_selected()).data.classes = $(this).val();
            }
        });
    }
});
