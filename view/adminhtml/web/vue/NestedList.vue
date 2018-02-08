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
                <div class="panel__collapse"
                     :class="{
                        'panel__collapse--up': collapsed,
                        'panel__collapse--down': !collapsed,
                        'panel__collapse--none': item.columns.length == 0,
                     }"
                     @click.prevent="collapsed = !collapsed"
                >
                </div>
                <div class="panel__heading-text" @click.prevent="collapsed = !collapsed">
                    {{item.title}}
                    <span class="panel__heading-type"
                          v-if="nodeType(item.type)"
                    >
                        {{ nodeType(item.type) }}
                    </span>
                </div>
                <div>
                    <button @click.prevent="editNode"
                            class="panel__buttom panel__buttom--edit"
                            title="Edit"
                    >
                    </button>
                    <button @click.prevent="appendEvent(list, index)"
                            class="panel__buttom panel__buttom--append"
                            title="Append"
                    >
                    </button>
                    <button @click.prevent="deleteEvent(list, index)"
                            class="panel__buttom panel__buttom--delete"
                            title="Delete"
                    >
                    </button>
                </div>
            </div>
            <div v-show="!collapsed">
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
                    <div v-else class="panel__empty-text">
                        Click
                        <button @click.prevent="appendEvent(list, index)"
                                class="panel__buttom panel__buttom--append"
                                title="Append"
                        >
                        </button>
                        to create sub node or drag and drop other nodes here.
                    </div>
                    <vddl-placeholder>
                        <div class="vddl-placeholder__inner"></div>
                    </vddl-placeholder>
                </vddl-list>
            </div>
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
                    editItem: false,
                    collapsed: true
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
                    this.collapsed = false;
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
                },
                editNode: function () {
                    this.editItem = !this.editItem;
                    this.collapsed = !this.editItem;
                }
            }
        });
    });
</script>
