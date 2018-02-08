<template>
    <div>
        <div class="panel">
            <div class="panel__heading v-row">
                <div class="panel__collapse"></div>
                <div class="panel__heading-text">
                    <span>Nodes</span>
                </div>
                <div>
                    <button @click.prevent="newNode"
                            class="panel__buttom panel__buttom--append"
                            title="Append"
                    >
                    </button>
                </div>
            </div>
            <div class="panel__body">
                <vddl-list class="panel__body--list"
                           :list="list"
                           :inserted="inserted"
                           effect-allowed="move"
                           :external-sources="true">
                    <template v-if="list.length > 0">
                        <snowdog-nested-list v-for="(item, index) in list"
                                             :key="item.id"
                                             :item="item"
                                             :list="list"
                                             :index="index"
                                             :selected="handleSelected"
                                             :selected-item="selectedItem"
                                             :delete="handleDelete"
                                             :append="handleAppend"
                                             :config="config"
                        >
                        </snowdog-nested-list>
                    </template>
                    <div v-else class="panel__empty-text">
                        Click
                        <button @click.prevent="newNode"
                                class="panel__buttom panel__buttom--append"
                                title="Append"
                        >
                        </button>
                        to create your first node.
                    </div>
                    <vddl-placeholder>
                        <div class="vddl-placeholder__inner"></div>
                    </vddl-placeholder>
                </vddl-list>
            </div>
        </div>
        <input type="hidden"
               name="serialized_nodes"
               :value="jsonList"
        />
    </div>
</template>

<script>

    define(["Vue"], function (Vue) {
        Vue.component("snowdog-menu", {
            template: template,
            props: ['list', 'config'],
            data() {
                return {
                    selectedItem: null
                };
            },
            methods: {
                copied(item) {
                    item.id++;
                },
                inserted(data) {
                    console.log(data);
                },
                handleSelected(item) {
                    this.selectedItem = item;
                },
                handleDelete: function (list, index) {
                    list.splice(index, 1);
                },
                handleAppend: function (list, index) {
                    list[index].columns.push({
                        'type': 'category',
                        'title': 'New node',
                        "id": new Date().getTime(),
                        "columns": []
                    });
                },
                newNode: function () {
                    this.list.push({
                        'type': 'category',
                        'title': 'New node',
                        "id": new Date().getTime(),
                        "columns": []
                    });
                }
            },
            computed: {
                jsonList: function () {
                    var list = this.list,
                        newList = [];

                    function itemMockup(item, parent) {
                        var updatedItem = {
                            id: item.id,
                            text: item.title,
                            data: {
                                classes: item.classes,
                                content: item.content,
                                target: item.target,
                                type: item.type
                            },
                            parent: parent
                        }
                        newList.push(updatedItem);
                        if (item.columns.length > 0) {
                            for (var i = 0; i < item.columns.length; i++) {
                                itemMockup(item.columns[i], item.id);
                            }
                        }
                    }

                    for (var i = 0; i < list.length; i++) {
                        itemMockup(list[i], '#')
                    }
                    return JSON.stringify(newList);
                }
            }
        });
    });
</script>
