define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function(options, element) {
        var buttonContainer = $(element),
            treeContainer   = $('#snowmenu_tree_container'),
            tree            = treeContainer.jstree(true);

        buttonContainer.children('button').click(function(e) {
            e.preventDefault();
            var selected = tree.get_selected();

            if (selected.length === 0) {
                selected = '#';
            }

            if ($(this).data('type')) {
                var data = {
                        data: {
                            type: $(this).data('type')
                        }
                    },
                    nodeId = tree.create_node(selected, data);

                tree.deselect_node(selected);
                tree.select_node(nodeId);
            }
            else if ($(this).data('remove') && selected !== '#') {
                tree.delete_node(selected);
            }
        });
    }
});
