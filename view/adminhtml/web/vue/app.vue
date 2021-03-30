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

        <input
            type="hidden"
            name="serialized_nodes"
            :value="jsonList"
        >
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
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
                       'id': new Date().getTime(),
                       'content': null,
                       'image': this.selectedItem.image,
                       'image_alt_text': this.selectedItem.image_alt_text,
                       'node_template': null,
                       'submenu_template': null,
                       'columns': []
                   });
               },
                handleDrop(data) {
                    data.item.id = new Date().getTime();
                    data.list.splice(data.index, 0, data.item);
                }
            },
            template: template
        });
    });
</script>
