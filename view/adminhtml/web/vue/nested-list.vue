<template>
    <vddl-draggable
        class="panel__body--item"
        :draggable="item"
        :index="index"
        :selected="selectedEvent"
        :delete="deleteEvent"
        :append="appendEvent"
        :duplicate="duplicateEvent"
        :wrapper="list"
        :class="{'selected': selectedItem === item}"
    >
        <div
            class="panel"
            @dragover="dragover"
            @dragenter="dragenter"
            @dragleave="dragleave"
        >
            <div class="panel__heading">
                <div
                    class="panel__collapse"
                    :class="{
                        'panel__collapse--up': collapsed,
                        'panel__collapse--down': !collapsed || draggedOver,
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
                        type="button"
                        class="panel__button panel__button--edit"
                        :title="config.translation.edit"
                        @click="editNode"
                    />

                    <button
                        type="button"
                        class="panel__button panel__button--append"
                        :title="config.translation.append"
                        @click="appendEvent(list, index)"
                    />

                    <button
                        type="button"
                        class="panel__button panel__button--duplicate"
                        :title="config.translation.duplicate"
                        @click="duplicateEvent(list, index)"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                            width="16"
                            height="16"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M408 112H184a72 72 0 0 0-72 72v224a72 72 0 0 0 72 72h224a72 72 0 0 0 72-72V184a72 72 0 0 0-72-72Zm-32.45 200H312v63.55c0 8.61-6.62 16-15.23 16.43A16 16 0 0 1 280 376v-64h-63.55c-8.61 0-16-6.62-16.43-15.23A16 16 0 0 1 216 280h64v-63.55c0-8.61 6.62-16 15.23-16.43A16 16 0 0 1 312 216v64h64a16 16 0 0 1 16 16.77c-.42 8.61-7.84 15.23-16.45 15.23Z"
                            />
                            <path d="M395.88 80A72.12 72.12 0 0 0 328 32H104a72 72 0 0 0-72 72v224a72.12 72.12 0 0 0 48 67.88V160a80 80 0 0 1 80-80Z" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="panel__button panel__button--delete"
                        :title="config.translation.delete"
                        @click="deleteEvent(list, index)"
                    />
                </div>
            </div>

            <div v-show="!collapsed || draggedOver">
                <vddl-list
                    class="panel__body"
                    :list="item.columns"
                    :drop="handleDrop"
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
                            :duplicate="duplicate"
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
                            class="panel__button panel__button--append"
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
                duplicate: {
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
            data() {
                return {
                    editItem: false,
                    collapsed: true,
                    draggedOver: false,
                    dragCounter: 0,
                    isDragging: false,
                }
            },
            methods: {
                selectedEvent(item) {
                    if (typeof(this.selected) === 'function') {
                        this.selected(item);
                    }
                },
                appendEvent(list, index) {
                    this.editItem = false;
                    this.collapsed = false;
                    if (typeof(this.append) === 'function') {
                        this.append(list[index].columns);
                    }
                },
                duplicateEvent(list, index) {
                    if (typeof(this.duplicate) === 'function') {
                        this.duplicate(list, index);
                    }
                },
                deleteEvent(list, index) {
                    this.editItem = false;
                    if (typeof(this.delete) === 'function') {
                        this.delete(list, index);
                    }
                },
                getNodeType(type) {
                    var nodeType = '';
                    if (type) {
                        nodeType = '(' + this.$root.config.nodeTypes[type] + ')';
                    }
                    return nodeType;
                },
                editNode() {
                    this.editItem = !this.editItem;
                    this.collapsed = !this.editItem;
                },
                dragover() {
                    this.isDragging = true;
                },
                dragenter() {
                    this.dragCounter++;
                    setTimeout(() => {
                        if (this.isDragging) {
                            this.draggedOver = true;
                        }
                    }, 500)
                },
                dragleave() {
                    this.dragCounter--;
                    this.isDragging = false;
                    setTimeout(() => {
                        if (this.dragCounter === 0) {
                            this.draggedOver = false;
                        }
                    }, 500)
                },
                handleDrop(data) {
                    this.drop(data)
                    this.draggedOver = false;
                    this.dragCounter = 0;
                    this.collapsed = false;
                },
            },
            template: template
        });
    });
</script>
