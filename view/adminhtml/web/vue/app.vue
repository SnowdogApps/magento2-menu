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
                    <nested-list
                        v-for="(item, index) in list"
                        :key="item.uuid"
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
                nodes: {
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
                    list: [],
                    selectedItem: null
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
                // check if serialized_nodes input loaded
                const checkElement = async selector => {
                    while (document.querySelector(selector) === null) {
                        await new Promise( resolve => requestAnimationFrame(resolve) )
                    }
                    return document.querySelector(selector);
                };

                const setUuidRecursive = (item) => {
                    item.uuid = this.uuid();
                    item.columns.map(column => setUuidRecursive(column))
                    return item;
                };

                // while loaded set JSON list as a value
                checkElement('[name="serialized_nodes"]').then(() => {
                    this.list = this.nodes.map(item => setUuidRecursive(item))
                    this.updateSerializedNodes(this.jsonList);
                });
            },
            methods: {
                uuid,
                setSelectedNode: function(item) {
                    this.selectedItem = item;
                },
                removeNode: function(list, index) {
                    list.splice(index, 1);
                },
                addNode: function(target) {
                    target.push({
                        id: this.uuid(),
                        uuid: this.uuid(),
                        type: 'category',
                        title: this.config.translation.addNode,
                        content: null,
                        image: null,
                        image_alt_text: '',
                        node_template: null,
                        submenu_template: null,
                        columns: [],
                        is_active: 0
                    });
                },
                handleDrop(data) {
                    data.item.uuid = this.uuid();
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
