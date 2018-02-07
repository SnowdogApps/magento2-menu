<template>
    <div class="admin__field field">
        <simple-field label="Custom URL"
                      id="custom_url"
                      type="text"
                      v-model="item.content"
        >
        </simple-field>
        <div class="admin__field field field-title">
            <label class="label admin__field-label"
                   for="snowmenu_custom_target"
            >
                Node target blank
            </label>
            <div class="admin__field-control control">
                <input class="checkbox"
                       type="checkbox"
                       id="snowmenu_custom_target"
                       true-value="_blank"
                       false-value="_self"
                       v-model="item.target"
                />
            </div>
        </div>
    </div>
</template>

<script>
    define(["Vue"], function (Vue) {
        Vue.component("custom_url", {
            template: template,
            props: ['config', 'item']
        });
    });
</script>
