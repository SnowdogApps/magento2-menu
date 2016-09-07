define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function ($) {
    return function(options, element) {
        var editorBlock = $(element);
        editorBlock.hide();
        var input = editorBlock.find('input');
        var treeContainer = $('#snowmenu_tree_container');
        var tree = treeContainer.jstree(true);
        treeContainer.on("changed.jstree", function (e, data) {
            if (data.node.data && data.node.data.type == options.type) {
                editorBlock.show();
                input.val(data.node.data.content);
            } else {
                editorBlock.hide();
                input.val(null);
            }
        });
        input.change(function () {
            var node = tree.get_selected();
            var selected = tree.get_node(node);
            selected.data.content = $(this).val();
            tree.deselect_node(node);
            tree.select_node(node);
        });
    }
});