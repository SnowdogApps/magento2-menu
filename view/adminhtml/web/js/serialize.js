define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function(options, element) {
        var serializedInput = $(element),
            treeContainer   = $('#snowmenu_tree_container'),
            nodeNameInput   = $('#snowmenu-node-name'),
            tree            = treeContainer.jstree(true);

        function serialize() {
            var data = tree.get_json(null, {
                flat: true,
                no_a_attr: true,
                no_li_attr: true,
                no_state: true
            });
            data = JSON.stringify(data);
            serializedInput.val(data);
        }
        treeContainer.on("changed.jstree", function() {
            serialize();
        });
        nodeNameInput.change(function() {
            serialize();
        });
        $(document).on("dnd_stop.vakata", serialize);
    }
});
