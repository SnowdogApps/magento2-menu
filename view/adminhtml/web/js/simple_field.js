define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function(options, element) {
        var editorBlock   = $(element),
            input         = editorBlock.find('input'),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        editorBlock.css('display', 'none');

        treeContainer.on("changed.jstree", function(e, data) {
            if (data.node.data && data.node.data.type === options.type) {
                editorBlock.css('display', 'block');
                input.val(data.node.data.content);
            }
            else {
                editorBlock.css('display', 'none');
                input.val(null);
            }
        });

        input.change(function() {
            var node     = tree.get_selected(),
                selected = tree.get_node(node);

            selected.data.content = $(this).val();
            tree.deselect_node(node);
            tree.select_node(node);
        });
    }
});
