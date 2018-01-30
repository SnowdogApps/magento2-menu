<template>
    <vddl-draggable class="panel__body--item"
                    :draggable="item"
                    :index="index"
                    :disable-if="disable"
                    :selected="selectedEvent"
                    :wrapper="list"
                    v-bind:class="{'selected': selectedItem === item}">
        <div class="panel padding" v-if="item.type === 'container'">
            <div class="panel__heading">
                <h3>
                    {{item.title}}
                    <span class="panel__heading-type"> - ({{ nodeType(item['data-type']) }})</span>
                </h3>
            </div>
            <vddl-list class="panel__body"
                       :list="item.columns"
                       :disable-if="disable"
                       :external-sources="true">
                <template v-if="selectedItem === item">
                    <snowdog-menu-type :item.sync="item" :config="$root.config"></snowdog-menu-type>
                </template>
                <template v-if="item.columns.length > 0">
                    <list v-for="(col, number) in item.columns"
                          :key="col.id" :item="col"
                          :list="item.columns"
                          :index="number"
                          :selected="selectedEvent"
                          :selected-item="selectedItem"
                          :disable="disable">
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
            props: ['item', 'list', 'index', 'selected', 'selectedItem', 'disable', 'config'],
            methods: {
                selectedEvent(item) {
                    if (typeof(this.selected) === 'function') {
                        this.selected(item);
                    }
                },
                removeEvent(item) {
                    console.log(item);
                },
                nodeType: function(type) {
                    return this.$root.config.nodeTypes[type];
                }
            }
        });
    });
</script>
