<template>
    <simple-field
        id="snowmenu_node_product"
        v-model="item.content"
        :label="config.translation.productId"
        type="textarea"
        @input="debouncedUpdateTitle"
    />
</template>

<script>
    define(['Vue'], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('product', {
            name: 'product',
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
            template: template,
            data() {
                return {
                    updateTitleTimeout: null
                };
            },
            methods: {
                debouncedUpdateTitle(value) {
                    clearTimeout(this.updateTitleTimeout);
                    this.updateTitleTimeout = setTimeout(() => {
                        this.updateTitle(value);
                    }, 300);
                },
                updateTitle(value) {
                    let adminPath = window.location.pathname.split('/snowmenu')[0];
                    fetch(`${adminPath}/snowmenu/node/productName?isAjax=true&product_id=${value}&form_key=${window.FORM_KEY}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                    })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        data.product_name && this.$set(this.item, 'title', data.product_name);
                    })
                    .catch(error => {
                        console.error('Error fetching product name:', error);
                    });
                }
            }
        });
    });
</script>
