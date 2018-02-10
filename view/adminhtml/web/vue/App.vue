<template>
    <div>
        <div class="panel">
            <div class="panel__heading v-row">
                <div class="panel__collapse"></div>
                <div class="panel__heading-text">
                    <span>{{ config.translation.nodes }}</span>
                </div>
                <div>
                    <button @click.prevent="newNode"
                            class="panel__buttom panel__buttom--append"
                            :title="config.translation.append"
                    >
                    </button>
                </div>
            </div>
            <div class="panel__body">
                <vddl-list class="panel__body--list"
                           :list="list"
                           effect-allowed="move"
                           :external-sources="true"
                           :config="config"
                >
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
                        {{ config.translation.click }}
                        <button @click.prevent="newNode"
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
                handleSelected(item) {
                    this.selectedItem = item;
                },
                handleDelete: function (list, index) {
                    list.splice(index, 1);
                },
                handleAppend: function (list, index) {
                    list[index].columns.push({
                        'type': 'category',
                        'title': this.config.translation.newNode,
                        "id": new Date().getTime(),
                        "columns": []
                    });
                },
                newNode: function () {
                    this.list.push({
                        'type': 'category',
                        'title': this.config.translation.newNode,
                        "id": new Date().getTime(),
                        "columns": []
                    });
                }
            },
            computed: {
                jsonList: function () {
                    return JSON.stringify(this.list);
                }
            }
        });
    });
</script>
