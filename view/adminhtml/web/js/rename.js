define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function ($) {
    return function(options, element) {
        var nodeNameInput = $(element);
        var treeContainer = $('#snowmenu_tree_container');
        var tree = treeContainer.jstree(true);
        treeContainer.on("changed.jstree", function (e, data) {
            nodeNameInput.val(data.instance.get_text(data.selected));
        });
        nodeNameInput.change(function () {
            tree.rename_node(tree.get_selected(), $(this).val());
        });
    }
});