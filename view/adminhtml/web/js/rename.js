define([
    'jquery',
    'snowMenuEditorSerialize',
    'snowMenuTree',
    'snowMenuEditorInit',
    'snowWysiwygSetup'
], function($, serialize) {
    return function(options, element) {
        var nodeInput     = $(element),
            treeContainer = $('#snowmenu_tree_container'),
            tree          = treeContainer.jstree(true);

        treeContainer.on("changed.jstree", function(e, data) {
            var node = data.instance.get_node(data.selected);

            if (options.type === 'node_classes') {
                if (node.data) {
                    nodeInput.val(node.data.classes);
                }
            }

            if (options.type === 'node_target') {
                if (node.data) {
                    nodeInput.prop('checked', !!node.data.target);
                }
            }
            serialize();
        });

        if (element.type === 'checkbox') {
            nodeInput.change(function() {
                if (options.type === 'node_target') {
                    tree.get_node(tree.get_selected()).data.target = this.checked;
                }
                serialize();
            });
        } else {
            nodeInput.on('input', function() {
                if (options.type === 'node_name') {
                    tree.rename_node(tree.get_selected(), $(this).val());
                }

                if (options.type === 'node_classes') {
                    tree.get_node(tree.get_selected()).data.classes = $(this).val();
                }
                serialize();
            });
        }
    }
});
