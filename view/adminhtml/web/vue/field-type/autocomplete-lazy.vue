<template>
    <div class="admin__field field field-title">
        <label class="label admin__field-label">
            {{ label }}
        </label>
        <div class="admin__field-control control">
            <treeselect
                v-if="isTree"
                v-model="selected"
                :options="optionsTree"
                :placeholder="placeholder"
                :default-expand-level="1"
                :clearable="false"
                :auto-load-root-options="false"
                :load-options="loadOptions"
                @input="setValue"
            >
                <template v-slot:value-label="{ node }">
                    {{ node.raw.full_label }}
                </template>
            </treeselect>

            <v-select
                v-else
                v-model="selected"
                :options="options"
                :placeholder="placeholder"
                :clearable="false"
                :disabled="isDisabled"
            >
                <template v-slot:option="option">
                    {{ option.label }}

                    <template v-if="option.store && option.store.length">
                        <span class="vs__dropdown-option__details">
                            {{ option.store.join(', ') }}
                        </span>
                    </template>
                </template>
            </v-select>
        </div>
    </div>
</template>

<script>
    const LOAD_CHILDREN_OPTIONS = 'LOAD_CHILDREN_OPTIONS';

    define(['Vue'], function(Vue) {
        Vue.component('autocomplete-lazy', {
            name: 'autocomplete-lazy',
            props: {
                label: {
                    type: String,
                    required: true
                },
                description: {
                    type: String,
                    required: true
                },
                options: {
                    type: Array,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                },
                itemKey: {
                    type: String,
                    required: true
                },
                defaultOptionValue: {
                    type: String,
                    default: 'default'
                },
                isTree: {
                    type: Boolean,
                    default: false
                },
                isDisabled: {
                    type: Boolean,
                    default: false
                }
            },
            data: () => ({
                hashTable: {},
                optionsTree: [],
                selected: null,
                initialLoaded: false
            }),
            computed: {
                placeholder: function() {
                    return this.config.translation.pleaseSelect + ' ' + this.label.toLocaleLowerCase();
                },
                loadedOptions() {
                    return this.options.filter(option => this.hashTable[option.id].loaded);
                },
                unloadedOptions() {
                    return this.options.filter(option => !this.hashTable[option.id].loaded);
                }
            },
            created() {
                this.setTree();
                this.setDefault();
            },
            methods: {
                setDefault() {
                    let optionValue;

                    for (let i = 0; i < this.options.length; i++) {
                        optionValue = this.options[i].value.toString();
                        if (optionValue === this.defaultOptionValue) {
                            this.defaultSelectedOption = this.options[i];
                        }
                    }
                },
                setInitial() {
                    let selectedOption = '';
                    const initialValue = this.item[this.itemKey];

                    if (!initialValue) {
                        this.selected = null;
                        return;
                    }

                    for (let i = 0; i < this.loadedOptions.length; i++) {
                        if (this.loadedOptions[i].value.toString() === this.item[this.itemKey]) {
                            selectedOption = this.isTree ? this.loadedOptions[i].value : this.loadedOptions[i];
                            break;
                        }
                    }

                    if (!selectedOption) {
                        selectedOption = this.defaultSelectedOption;
                    }

                    this.selected = selectedOption;
                    this.initialLoaded = true;

                },
                setTree() {
                    const hashTable = this.options.reduce((a, b) => {
                        a[b.id] = { ...b, children: null, loaded: false };
                        return a;
                    }, {});

                    this.options.forEach(item => {
                        if (item.parent_id && !(hashTable[item.parent_id])) {
                            this.optionsTree.push(hashTable[item.id]);
                            hashTable[item.id].loaded = true;
                        }
                    });

                    this.hashTable = hashTable;
                },
                loadOptions({ action, parentNode, callback }) {
                    let deep = false;

                    const initialValue = this.item[this.itemKey];
                    deep = initialValue && !this.initialLoaded;

                    if (action === LOAD_CHILDREN_OPTIONS) {
                        parentNode.children = [];

                        const loadChildren = (parent) => {
                            this.unloadedOptions.forEach(item => {
                                if (item.parent_id === parent.id) {
                                    parent.children.push({...item, children: null });
                                    this.hashTable[item.id].loaded = true;
                                }
                            })
                            if (!parent.children.length) {
                                delete parent.children;
                            }
                            return parent;
                        }
                        loadChildren(parentNode);

                        // this little maneuver's gonna cost us 51 years
                        if (deep) {
                            let toLoad = {},
                                parent = parentNode,
                                id = initialValue;

                            while (!this.hashTable[id].loaded) {
                                toLoad[this.hashTable[id].parent_id] = this.hashTable[id].id;
                                id = this.hashTable[id].parent_id;
                            }
                            toLoad[this.hashTable[id].parent_id] = this.hashTable[id].id;

                            while (Object.keys(toLoad).length) {
                                const childId = toLoad[parent.id];
                                let newParent = parent.children.find(child => child.id === childId);
                                newParent.children = [];
                                newParent = loadChildren(newParent);
                                delete toLoad[parent.id];
                                parent = newParent;
                            }

                            this.setInitial();
                        }

                        callback();
                    }
                },
                setValue(option) {
                    if (!this.initialLoaded) {
                        this.initialLoaded = true;
                    }

                    if (option && typeof option === 'object') {
                        this.item[this.itemKey] = option.value.toString();
                    }
                    else if (option && typeof option === 'string') {
                        this.item[this.itemKey] = option;
                    }
                    else {
                      this.item[this.itemKey] = this.defaultSelectedOption ? this.defaultSelectedOption.value.toString() : '';
                    }
                }
            },
            template: template
        });
    });
</script>
