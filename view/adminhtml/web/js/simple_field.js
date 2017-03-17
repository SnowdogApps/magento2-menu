define([
    'jquery',
    'snowMenuEditorSerialize',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($, snowSerialize) {
    return function(options, element) {
        var editorParent  = $(element).parent(),
            editorBlock   = $(element).detach(),
            nodeType      = editorBlock.attr('data-node-type'),
            nodeNameInput   = editorBlock.find('#snowmenu_node_name_' + nodeType),
            nodeClassInput  = editorBlock.find('#snowmenu_node_classes'),
            input         = editorBlock.find('.node-value-field input'),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        treeContainer.on("changed.jstree", function(e, data) {
            var editor = tinyMceEditors.get(nodeNameInput.attr('id'));
            if(editor) {
                editor.turnOff();
            }

            if (data.node.data && data.node.data.type === options.type) {
                var value = data.node.data.content,
                    currentEditorNode = input.attr('current-node-id');

                if(!currentEditorNode || data.node.id != currentEditorNode) {
                    input.attr('current-node-id', data.node.id);
                    editorParent.append(editorBlock);
                    nodeNameInput.val(data.instance.get_text(data.selected));
                }

                input.val(data.node.data.content);
            }
            else {
                editorBlock.detach();
                input.val('');
                input.attr('current-node-id', '');
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
