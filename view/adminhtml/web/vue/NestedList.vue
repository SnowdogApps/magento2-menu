<template>
    <vddl-draggable class="panel__body--item"
                    :draggable="item"
                    :index="index"
                    :selected="selectedEvent"
                    :delete="deleteEvent"
                    :append="appendEvent"
                    :wrapper="list"
                    v-bind:class="{'selected': selectedItem === item}">
        <div class="panel padding">
            <div class="panel__heading v-row">
                <div>
                    {{item.title}}
                    <span class="panel__heading-type"
                          v-if="nodeType(item['type'])"
                    >
                        {{ nodeType(item['type']) }}
                    </span>
                </div>
                <div>
                    <button @click.prevent="editItem = ! editItem">
                        <span v-if="!editItem">Edit</span>
                        <span v-else>Close</span>
                    </button>
                    <button @click.prevent="appendEvent(list, index)">Append</button>
                    <button @click.prevent="deleteEvent(list, index)">Delete</button>
                </div>
            </div>
            <vddl-list class="panel__body"
                       :list="item.columns"
                       :external-sources="true"
            >
                <template v-if="editItem">
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
                          :delete="deleteEvent"
                          :append="appendEvent"
                    >
                    </list>
                </template>
                <vddl-placeholder class="red">Insert here</vddl-placeholder>
            </vddl-list>
        </div>
    </vddl-draggable>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("snowdog-nested-list", {
            template: template,
            name: 'list',
            props: ['item', 'list', 'index', 'selected', 'selectedItem', 'delete', 'append', 'config'],
            data: function () {
                return {
                    editItem: false
                }
            },
            methods: {
                selectedEvent: function (item) {
                    if (typeof(this.selected) === 'function') {
                        this.selected(item);
                    }
                },
                appendEvent: function (list, index) {
                    this.editItem = false;
                    if (typeof(this.append) === 'function') {
                        this.append(list, index);
                    }
                },
                deleteEvent: function (list, index) {
                    this.editItem = false;
                    if (typeof(this.delete) === 'function') {
                        this.delete(list, index);
                    }
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
