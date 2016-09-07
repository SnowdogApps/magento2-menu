define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function ($) {
    return function(options, element) {
        var nodeInput = $(element);
        var treeContainer = $('#snowmenu_tree_container');
        var tree = treeContainer.jstree(true);
        treeContainer.on("changed.jstree", function (e, data) {
            if(options.type == 'name') {
                nodeInput.val(data.instance.get_text(data.selected));
            } else if (options.type == 'classes') {
                var node = data.instance.get_node(data.selected);
                if(node.data) {
                    nodeInput.val(node.data.classes);
                }
            }
        });
        nodeInput.change(function () {
            if(options.type == 'name') {
                tree.rename_node(tree.get_selected(), $(this).val());
            } else if (options.type == 'classes') {
                tree.get_node(tree.get_selected()).data.classes = $(this).val();
            }
        });
    }
});