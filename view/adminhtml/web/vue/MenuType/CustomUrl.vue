<template>
    <simple-field label="Custom URL"
                  id="custom_url"
                  type="text"
                  v-model="item.content"
    >
    </simple-field>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("custom_url", {
            template: template,
            props: ['config', 'item'],
        });
    });
</script>
