<template>
    <vddl-draggable class="panel__body--item"
                    :draggable="item"
                    :index="index"
                    :selected="selectedEvent"
                    :wrapper="list"
                    v-bind:class="{'selected': selectedItem === item}">
        <div class="panel padding" v-if="item.type === 'container'">
            <div class="panel__heading v-row">
                <div>
                    {{item.title}}
                    <span class="panel__heading-type"
                          v-if="nodeType(item['data-type'])"
                    >
                        {{ nodeType(item['data-type']) }}
                    </span>
                </div>
                <div>
                    <button @click.prevent="editedItem = ! editedItem">
                        <span v-if="!editedItem">Edit</span>
                        <span v-else>Close</span>
                    </button>
                    <button @click.prevent="handleInsert">Append</button>
                    <button @click.prevent="handleRemove">Delete</button>
                </div>
            </div>
            <vddl-list class="panel__body"
                       :list="item.columns"
                       :external-sources="true"
            >
                <template v-if="editedItem">
                    <snowdog-menu-type :item.sync="item"
                                       :config="$root.config"
                    >
                    </snowdog-menu-type>
                </template>
                <template v-if="item.columns.length > 0">
                    <list v-for="(col, number) in item.columns"
                          :key="col.id"
                          :item="col"
                          :list="item.columns"
                          :index="number"
                          :selected="selectedEvent"
                          :selected-item="selectedItem"
                    >
                    </list>
                </template>
                <vddl-placeholder class="red">Insert here</vddl-placeholder>
            </vddl-list>
        </div>
        <p v-else>
            {{item.type}} {{item.id}}
        </p>
    </vddl-draggable>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("snowdog-nested-list", {
            template: template,
            name: 'list',
            props: ['item', 'list', 'index', 'selected', 'selectedItem', 'config'],
            data: function() {
                return {
                    editedItem: false,
                    itemMock : {
                        "type": "container",
                        'data-type': 'category',
                        'title': 'New node',
                        "id": 4,
                        "columns": []
                    }
                }
            },
            methods: {
                selectedEvent(item) {
                    if (typeof(this.selected) === 'function') {
                        this.selected(item);
                    }
                },
                handleRemove: function () {
                    this.editedItem = false;
                    this.list.splice(this.index, 1);
                },
                handleInsert: function (item) {
                    this.editedItem = false;
                    this.list[this.index].columns.push(this.itemMock);
                },
                nodeType: function (type) {
                    var nodeType = '';
                    if (type) {
                        nodeType = '(' + this.$root.config.nodeTypes[type] + ')';
                    }
                    return nodeType;
                }
            }
        });
    });
</script>
