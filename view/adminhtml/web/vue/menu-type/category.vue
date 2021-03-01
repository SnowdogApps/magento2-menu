<template>
    <auto-complete
        :is-tree="true"
        :label="config.translation.category"
        :description="config.translation.categoryId"
        :item="item"
        :item-key="'content'"
        :options="options"
        :options-tree="optionsTree"
        :config="config"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('category', {
            props: {
                config: {
                    type: Object,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                }
            },
            data() {
                return {
                    options: this.config.fieldData.category.snowMenuAutoCompleteField.options
                }
            },
            computed: {
                optionsTree() {
                    const hashTable = {},
                          optionsTree = [];

                    this.options.forEach(item => hashTable[item.id] = {...item});
                    this.options.forEach(item => {
                        if (item.parent_id && hashTable[item.parent_id]) {
                            hashTable[item.parent_id].children = [
                                ...(hashTable[item.parent_id].children || []),
                                hashTable[item.id]
                            ];
                        } else {
                            optionsTree.push(hashTable[item.id]);
                        }
                    });

                    return optionsTree;
                }
            },
            template: template
        });
    });
</script>
