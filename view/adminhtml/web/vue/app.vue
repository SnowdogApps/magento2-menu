<template>
    <div class="panel panel--open">
        <div class="panel__heading">
            <div class="panel__collapse" />

            <div class="panel__heading-text">
                <span>{{ config.translation.nodes }}</span>
            </div>

            <div>
                <button
                    class="panel__buttom panel__buttom--append"
                    :title="config.translation.append"
                    @click.prevent="addNode(list)"
                />
            </div>
        </div>

        <div class="panel__body">
            <vddl-list
                class="panel__body--list"
                :list="list"
                effect-allowed="move"
                :external-sources="true"
                :drop="handleDrop"
            >
                <template v-if="list.length > 0">
                    <snowdog-nested-list
                        v-for="(item, index) in list"
                        :key="item.id"
                        :item="item"
                        :list="list"
                        :index="index"
                        :selected="setSelectedNode"
                        :selected-item="selectedItem"
                        :delete="removeNode"
                        :append="addNode"
                        :drop="handleDrop"
                        :config="config"
                    />
                </template>

                <div
                    v-else
                    class="panel__empty-text"
                >
                    {{ config.translation.click }}
                    <button
                        class="panel__buttom panel__buttom--append"
                        :title="config.translation.append"
                        @click.prevent="addNode(list)"
                    />
                    {{ config.translation.createFirstNode }}
                </div>

                <vddl-placeholder>
                    <div class="vddl-placeholder__inner" />
                </vddl-placeholder>
            </vddl-list>
        </div>
    </div>
</template>

<script>
    define([
        'Vue',
        'uuid'
    ], function(Vue, uuid) {
        Vue.component('snowdog-menu', {
            props: {
                list: {
                    type: Array,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                }
            },
            data: function() {
                return {
                    selectedItem: false
                };
            },
            computed: {
                jsonList: function() {
                    return JSON.stringify(this.list);
                }
            },
            watch: {
                jsonList: function (newValue) {
                    this.updateSerializedNodes(newValue)
                }
            },
            mounted () {
                const self = this;
                // check if serialized_nodes input loaded
                const checkElement = async selector => {
                    while (document.querySelector(selector) === null) {
                        await new Promise( resolve => requestAnimationFrame(resolve) )
                    }
                    return document.querySelector(selector);
                };
                // while loaded set JSON list as a value
                checkElement('[name="serialized_nodes"]').then(() => {
                    self.updateSerializedNodes(self.jsonList);
                });
            },
            methods: {
                setSelectedNode: function(item) {
                    this.selectedItem = item;
                },
                removeNode: function(list, index) {
                    list.splice(index, 1);
                },
               addNode: function(target) {
                   target.push({
                       'type': 'category',
                       'title': this.config.translation.addNode,
                       'id': uuid(),
                       'content': null,
                       'node_template': null,
                       'image': this.selectedItem.image,
                       'image_alt_text': this.selectedItem.image_alt_text,
                       'node_template': null,
                       'submenu_template': null,
                       'columns': [],
                       'is_active': 0
                   });
                },
                handleDrop(data) {
                    data.item.id = uuid();
                    data.list.splice(data.index, 0, data.item);
                },
                updateSerializedNodes(value) {
                    const updateEvent = new Event('change');
                    const serializedNodeInput = document.querySelector('[name="serialized_nodes"]');
                    // update serialized_nodes input value
                    serializedNodeInput.value = value;
                    // trigger change event to set value
                    serializedNodeInput.dispatchEvent(updateEvent);
                }
            },
            template: template
        });
    });
</script>
