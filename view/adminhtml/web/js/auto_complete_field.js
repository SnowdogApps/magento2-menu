define([
    'jquery',
    'underscore',
    'snowMenuEditorSerialize',
    'snowMenuTree',
    'snowMenuEditorInit',
    'snowWysiwygSetup'
], function($, _, snowSerialize) {
    return function(options, element) {
        var editorParent          = $(element).parent(),
            editorBlock           = $(element).detach(),
            nodeType              = editorBlock.attr('data-node-type'),
            input                 = editorBlock.find('.node-value-field input'),
            label                 = editorBlock.find('.selected-option__value'),
            nodeNameInput         = editorBlock.find('#snowmenu_node_name_' + nodeType),
            nodeClassInput        = editorBlock.find('#snowmenu_node_classes'),
            configuration         = options.options,
            configurationKeys     = Object.keys(options.options),
            invertedConfiguration = _.invert(configuration),
            treeContainer         = $('#snowmenu_tree_container'),
            tree                  = treeContainer.jstree(true);

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
                editorBlock.detach();
                input.val('');
                input.attr('current-node-id', '');
                label.html('');
                label.removeClass('admin__field-error');
            }
        });

        nodeNameInput.change(function() {
            snowSerialize();
        });

        nodeClassInput.change(function() {
            snowSerialize();
        });
    }
});
