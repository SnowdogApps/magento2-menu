<template>
    <div class="admin__field field">
        <simple-field
            :label="config.translation.customURL"
            id="custom_url"
            type="text"
            v-model="item.content"
        >
        </simple-field>
        <div class="admin__field field field-title">
            <label
                class="label admin__field-label"
                for="snowmenu_custom_target"
            >
                {{ config.translation.targetLabel }}
            </label>
            <div class="admin__field-control control">
                <input
                    class="checkbox"
                    type="checkbox"
                    id="snowmenu_custom_target"
                    true-value="1"
                    false-value="0"
                    v-model="item.target"
                />
            </div>
        </div>
    </div>
</template>

<script>
define(["Vue"], function(Vue) {
    Vue.component("custom_url", {
        template: template,
        props: ['config', 'item']
    });
});
</script>
