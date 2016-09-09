define([
    'jquery',
    'underscore',
    'snowMenuTree',
    'snowMenuEditorInit'
], function($, _) {
    return function(options, element) {
        var editorBlock           = $(element),
            input                 = editorBlock.find('input'),
            label                 = editorBlock.find('.selected-option__value'),
            configuration         = options.options,
            configurationKeys     = Object.keys(options.options),
            invertedConfiguration = _.invert(configuration),
            treeContainer         = $('#snowmenu_tree_container'),
            tree                  = treeContainer.jstree(true);

        editorBlock.css('display', 'none');

        input.autocomplete({
            source: configurationKeys,
            autoFocus: true,
            minLength: 0,
            change: function() {
                var node = tree.get_selected(),
                    selected = tree.get_node(node),
                    value = $(this).val();

                if (configuration[value]) {
                    label.html(configuration[value]);
                    selected.data.content = configuration[value];
                    label.removeClass('admin__field-error');
                }
                else {
                    label.html(options.message);
                    label.addClass('admin__field-error');
                }

                tree.deselect_node(node);
                tree.select_node(node);
            }
        });

        treeContainer.on("changed.jstree", function(e, data) {
            if (data.node.data && data.node.data.type === options.type) {
                var value = data.node.data.content;

                editorBlock.css('display', 'block');
                input.val(invertedConfiguration[value]);

                if (configuration[invertedConfiguration[value]]) {
                    label.html(value);
                    label.removeClass('admin__field-error');
                }
                else {
                    label.html(options.message);
                    label.addClass('admin__field-error');
                }
            }
            else {
                editorBlock.css('display', 'none');
                input.val('');
                label.html('');
                label.removeClass('admin__field-error');
            }
        });
    }
});
