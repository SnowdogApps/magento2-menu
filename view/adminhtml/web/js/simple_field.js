define([
    'jquery',
    'snowMenuEditorSerialize',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($, snowSerialize) {
    return function(options, element) {
        var editorParent  = $(element).parent(),
            editorBlock   = $(element).detach(),
            nodeNameInput   = editorBlock.find('#snowmenu_node_name'),
            nodeClassInput  = editorBlock.find('#snowmenu_node_classes'),
            input         = editorBlock.find('.node-value-field input'),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        treeContainer.on("changed.jstree", function(e, data) {
            if (data.node.data && data.node.data.type === options.type) {
                editorParent.append(editorBlock);
                input.val(data.node.data.content);
                nodeNameInput.val(data.instance.get_text(data.selected));
            }
            else {
                editorBlock.detach();
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

        nodeNameInput.change(function() {
            snowSerialize();
        });

        nodeClassInput.change(function() {
            snowSerialize();
        });
    }
});
