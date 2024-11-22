<template>
    <div>
        <simple-field
            id="snowmenu_node_product"
            v-model="item.content"
            :label="config.translation.productId"
            type="textarea"
            @input="updateTitle"
        />
    </div>
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
            methods: {
                updateTitle(value) {
                    let adminPath = window.location.pathname.split('/snowmenu')[0];
                    fetch(`${adminPath}/snowmenu/node/productName?isAjax=true&product_id=${value}&form_key=${window.FORM_KEY}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                    })
                    .then((response) => {
                        console.log(response)
                        // return response.json();
                    })
                    // .then(data => {
                        // this.$set(this.item, 'title', data.name);
                    // })
                    .catch(error => {
                        console.error('Error fetching product name:', error);
                        // this.$set(this.item, 'title', value);
                    });
                }
            }
        });
    });
</script>
