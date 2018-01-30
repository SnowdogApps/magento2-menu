<template>
    <div class="nested">
        <div class="panel">
            <div class="panel__heading">
                <h3>Menu</h3>
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
                                             :config="config">
                        </snowdog-nested-list>
                    </template>
                    <vddl-placeholder class="red">Insert here</vddl-placeholder>
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
                                type: item['data-type']
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
