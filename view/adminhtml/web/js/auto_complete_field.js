define([
        'jquery',
        'snowMenuTree',
        'snowMenuEditorInit'
    ], function ($) {
        return function (options, element) {
            var editorBlock = $(element);
            editorBlock.hide();
            var input = editorBlock.find('input');
            var span = editorBlock.find('span');
            var source = [];
            var reverse = {};
            var treeContainer = $('#snowmenu_tree_container');
            var tree = treeContainer.jstree(true);
            for (var key in options.options) {
                source.push(key);
                reverse[options.options[key]] = key;
            }
            input.autocomplete({
                source: source,
                change: function () {
                    var node = tree.get_selected();
                    var selected = tree.get_node(node);
                    var value = $(this).val();
                    if (options.options[value]) {
                        span.html(options.options[value]);
                        selected.data.content = options.options[value];
                        span.removeClass('admin__field-error');
                    } else {
                        span.html(options.message);
                        span.addClass('admin__field-error');
                    }
                    tree.deselect_node(node);
                    tree.select_node(node);
                }
            });
            treeContainer.on("changed.jstree", function (e, data) {
                if (data.node.data && data.node.data.type == options.type) {
                    editorBlock.show();
                    var value = data.node.data.content;
                    input.val(reverse[value]);
                    if (options.options[reverse[value]]) {
                        span.html(value);
                        span.removeClass('admin__field-error');
                    } else {
                        span.html(options.message);
                        span.addClass('admin__field-error');
                    }
                } else {
                    editorBlock.hide();
                    input.val(null);
                    span.html(false);
                    span.removeClass('admin__field-error');
                }
            });
        }
    }
);