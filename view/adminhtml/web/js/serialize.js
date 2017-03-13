define([
    'jquery',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($) {
    return function() {
        var treeContainer   = $('#snowmenu_tree_container'),
            tree            = treeContainer.jstree(true),
            serializedInput = $('#serialized_nodes');

        if(serializedInput !== 'undefinied' && tree !== false){
            var data = tree.get_json(null, {
                flat: true,
                no_a_attr: true,
                no_li_attr: true,
                no_state: true
            });
            data = JSON.stringify(data);
            serializedInput.val(data);
        }
    }
});
