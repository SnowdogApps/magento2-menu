<template>
    <div class="admin__fieldset fieldset-wide">
        <simple-field
            id="custom_url"
            v-model="item.content"
            :label="config.translation.customURL"
            type="text"
        />

        <div
            class="
                admin__field
                field
                field-title
                admin__scope-old
            "
        >
            <label
                class="label admin__field-label"
                for="snowmenu_custom_target"
            >
                {{ config.translation.targetLabel }}
            </label>

            <div class="admin__field-control control">
                <input
                    id="snowmenu_custom_target"
                    v-model="item.target"
                    class="checkbox"
                    type="checkbox"
                    true-value="1"
                    false-value="0"
                >
            </div>
        </div>
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
        // eslint-disable-next-line vue/component-definition-name-casing
        Vue.component('custom_url', {
            name: 'custom-url',
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
            template: template
        });
    });
</script>
