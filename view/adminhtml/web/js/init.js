define([
    'jquery',
    'snowMenuEditorSerialize',
    'snowMenuTree'
], function($, snowSerialize) {
    return function(options, element) {
        var treeContainer = $(element);
        treeContainer.jstree({
            "core": {
                "check_callback": true,
                "multiple": false,
                "themes": {
                    "icons": false
                }
            },
            "plugins": ["dnd"]
        });

        treeContainer.on("changed.jstree", snowSerialize);
        $(document).on("dnd_stop.vakata", snowSerialize);
    }
});
