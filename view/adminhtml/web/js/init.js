define([
    'jquery',
    'snowMenuTree'
], function($) {
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
    }
});
