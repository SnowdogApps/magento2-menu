<template>
    <vddl-draggable
        class="panel__body--item"
        :draggable="item"
        :index="index"
        :selected="selectedEvent"
        :delete="deleteEvent"
        :append="appendEvent"
        :wrapper="list"
        :class="{'selected': selectedItem === item}"
    >
        <div class="panel">
            <div class="panel__heading">
                <div
                    class="panel__collapse"
                    :class="{
                        'panel__collapse--up': collapsed,
                        'panel__collapse--down': !collapsed,
                        'panel__collapse--none': item.columns.length == 0,
                    }"
                    @click.prevent="collapsed = !collapsed"
                />

                <div
                    class="panel__heading-text"
                    @click.prevent="collapsed = !collapsed"
                >
                    {{ item.title }}

                    <span
                        v-if="getNodeType(item.type)"
                        class="panel__heading-type"
                    >
                        {{ getNodeType(item.type) }}
                    </span>
                </div>

                <div>
                    <button
                        class="panel__buttom panel__buttom--edit"
                        :title="config.translation.edit"
                        @click.prevent="editNode"
                    />

                    <button
                        class="panel__buttom panel__buttom--append"
                        :title="config.translation.append"
                        @click.prevent="appendEvent(list, index)"
                    />

                    <button
                        class="panel__buttom panel__buttom--delete"
                        :title="config.translation.delete"
                        @click.prevent="deleteEvent(list, index)"
                    />
                </div>
            </div>

            <div v-show="!collapsed">
                <vddl-list
                    class="panel__body"
                    :list="item.columns"
                    :drop="drop"
                    :external-sources="true"
                >
                    <template v-if="editItem">
                        <vddl-nodrag>
                            <menu-type
                                :item.sync="item"
                                :config="config"
                            />
                        </vddl-nodrag>
                    </template>

                    <template v-if="item.columns.length > 0">
                        <nested-list
                            v-for="(col, number) in item.columns"
                            :key="col.uuid"
                            :item="col"
                            :list="item.columns"
                            :index="number"
                            :selected="selectedEvent"
                            :selected-item="selectedItem"
                            :delete="deleteEvent"
                            :append="append"
                            :drop="drop"
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
                            @click.prevent="appendEvent(list, index)"
                        />
                        {{ config.translation.createSubNode }}
                    </div>

                    <vddl-placeholder>
                        <div class="vddl-placeholder__inner" />
                    </vddl-placeholder>
                </vddl-list>
            </div>
        </div>
    </vddl-draggable>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('nested-list', {
            name: 'nested-list',
            props: {
                item: {
                    type: Object,
                    required: true
                },
                list: {
                    type: Array,
                    required: true
                },
                index: {
                    type: Number,
                    required: true
                },
                selected: {
                    type: Function,
                    required: true
                },
                selectedItem: {
                    type: [Object, Boolean],
                    default: false
                },
                delete: {
                    type: Function,
                    required: true
                },
                append: {
                    type: Function,
                    required: true
                },
                drop: {
                    type: Function,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                },
            },
            data: function() {
                return {
                    editItem: false,
                    collapsed: true
                }
            },
            methods: {
                selectedEvent: function(item) {
                    if (typeof(this.selected) === 'function') {
                        this.selected(item);
                    }
                },
                appendEvent: function(list, index) {
                    this.editItem = false;
                    this.collapsed = false;
                    if (typeof(this.append) === 'function') {
                        this.append(list[index].columns);
                    }
                },
                deleteEvent: function(list, index) {
                    this.editItem = false;
                    if (typeof(this.delete) === 'function') {
                        this.delete(list, index);
                    }
                },
                getNodeType: function(type) {
                    var nodeType = '';
                    if (type) {
                        nodeType = '(' + this.$root.config.nodeTypes[type] + ')';
                    }
                    return nodeType;
                },
                editNode: function() {
                    this.editItem = !this.editItem;
                    this.collapsed = !this.editItem;
                }
            },
            template: template
        });
    });
</script>
