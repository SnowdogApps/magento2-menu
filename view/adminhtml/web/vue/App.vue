<template>
    <div class="nested">
        <div class="v-row">
            <div class="v-col--80">
                <div class="panel">
                    <div class="panel__heading">
                        <h3>Menu</h3>
                    </div>
                    <div class="panel__body">
                        <vddl-list class="panel__body--list"
                                   :list="list"
                                   :inserted="inserted"
                                   effect-allowed="move"
                                   :disable-if="disable"
                                   :external-sources="true">
                            <template v-if="list.length > 0">
                                <snowdog-nested-list v-for="(item, index) in list"
                                                     :key="item.id"
                                                     :item="item"
                                                     :list="list"
                                                     :index="index"
                                                     :selected="handleSelected"
                                                     :selected-item="selectedItem"
                                                     :config="config"
                                                     :disable="disable">
                                </snowdog-nested-list>
                            </template>
                            <vddl-placeholder class="red">Insert here</vddl-placeholder>
                        </vddl-list>
                    </div>
                </div>
            </div>
            <div class="v-col--20">
                <div class="new-elements">
                    <div class="panel panel--info">
                        <div class="panel__heading">
                            <h3>New Elements</h3>
                        </div>
                        <div class="panel__body">
                            <vddl-draggable class="button"
                                            :draggable="containerMock"
                                            :copied="copied"
                                            effect-allowed="copy">
                                Add Container
                            </vddl-draggable>
                        </div>
                    </div>
                </div>
                <div class="new-elements disable-element">
                    <div class="panel panel--info">
                        <div class="panel__heading">
                            <h3>Toggle Disable</h3>
                        </div>
                        <div class="panel__body">
                            <div class="button" @click="toggleDisable">
                                Disable: {{disable}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="selected-item" v-if="selectedItem">
                    <div class="panel panel--info">
                        <div class="panel__heading">
                            <h3>Selected</h3>
                        </div>
                        <div class="panel__body">
                            {{selectedItem.type}} {{selectedItem.id}}
                        </div>
                    </div>
                </div>
                <div class="ashcan">
                    <div class="panel panel--info">
                        <div class="panel__heading">
                            <h3>Ashcan</h3>
                        </div>
                        <vddl-list :list="[]" class="panel__body">
                            <img class="ashcan-logo" src="../assets/ashcan.png" alt=""/>
                        </vddl-list>
                    </div>
                </div>
            </div>
            <input type="hidden"
                   name="serialized_nodes"
                   :value="jsonList"
            />
        </div>
    </div>
</template>

<script>

    define(["Vue"], function (Vue) {
        Vue.component("snowdog-menu", {
            template: template,
            props: ['list', 'config'],
            data() {
                return {
                    selectedItem: null,
                    containerMock: {
                        "type": "container",
                        'data-type': '',
                        'title': 'New node',
                        "id": 4,
                        "columns": []
                    },
                    disable: false,
                };
            },
            methods: {
                copied(item) {
                    item.id++;
                },
                inserted(data) {
                    console.log(data);
                },
                toggleDisable() {
                    this.disable = !this.disable;
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
