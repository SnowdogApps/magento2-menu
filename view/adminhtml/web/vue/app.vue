<template>
    <div class="panel">
        <div class="panel__heading">
            <div class="panel__collapse"></div>
            <div class="panel__heading-text">
                <span>{{ config.translation.nodes }}</span>
            </div>
            <div>
                <button
                    @click.prevent="addNode(list)"
                    class="panel__buttom panel__buttom--append"
                    :title="config.translation.append"
                >
                </button>
            </div>
        </div>
        <div class="panel__body">
            <vddl-list
                class="panel__body--list"
                :list="list"
                effect-allowed="move"
                :external-sources="true"
                :drop="handleDrop"
                :config="config"
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
                    >
                    </snowdog-nested-list>
                </template>
                <div v-else class="panel__empty-text">
                    {{ config.translation.click }}
                    <button
                        @click.prevent="addNode(list)"
                        class="panel__buttom panel__buttom--append"
                        :title="config.translation.append"
                    >
                    </button>
                    {{ config.translation.createFirstNode }}
                </div>
                <vddl-placeholder>
                    <div class="vddl-placeholder__inner"></div>
                </vddl-placeholder>
            </vddl-list>
        </div>
        <input
            type="hidden"
            name="serialized_nodes"
            :value="jsonList"
        />
    </div>
</template>

<script>
define(["Vue"], function(Vue) {
    Vue.component("snowdog-menu", {
        template: template,
        props: ['list', 'config'],
        data: function() {
            return {
                selectedItem: null
            };
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
                    "id": new Date().getTime(),
                    "content": null,
                    "columns": []
                });
            },
            handleDrop(data) {
                data.item.id = new Date().getTime();
                data.list.splice(data.index, 0, data.item);
            }
        },
        computed: {
            jsonList: function() {
                return JSON.stringify(this.list);
            }
        }
    });
});
</script>
