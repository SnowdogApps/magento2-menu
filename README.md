[![Packagist](https://img.shields.io/packagist/v/snowdog/module-menu?style=for-the-badge)](https://packagist.org/packages/snowdog/module-menu)
[![Packagist](https://img.shields.io/packagist/dt/snowdog/module-menu?style=for-the-badge)](https://packagist.org/packages/snowdog/module-menu)
[![Packagist](https://img.shields.io/packagist/dm/snowdog/module-menu?style=for-the-badge)](https://packagist.org/packages/snowdog/module-menu)

# Magento 2 Menu
Provides powerful menu editor to replace category based menus in Magento 2.

## Use
List of menus is located in Admin Panel under `Content > Elements > Menus`.

Following example shows how to replace default Magento 2 menu, by the user-defined menu with identifier `main`.

```xml
<referenceBlock name="catalog.topnav" remove="true"/>
<referenceBlock name="store.menu">
  <block name="main.menu" class="Snowdog\Menu\Block\Menu">
     <arguments>
        <argument name="menu" xsi:type="string">main</argument>
     </arguments>
  </block>
</referenceBlock>
```

## Overwriting templates per menu ID
You have to add new folder with menu ID and add same structure like in default folder. For example, to overwrite templates of menu with ID `menu_main` the folders structure should looks like this:
```
Snowdog_Menu
  └─ templates
    └─ menu_main
      │- menu
      │  │- node_type
      │  │  │- category.phtml
      │  │  └─ ...
      │  └─ sub_menu.phtml
      └─ menu.phtml
```

## Adding new types of nodes
To add new type node you have to add:
 - new backend block that also implements `\Snowdog\Menu\Api\NodeTypeInterface` and is defined in di.xml
 - create new vue component for new type node and define it in di.xml

Backend block will be directly injected into menu editor.

### New node type in admin panel
Menu UI in admin panel is build with Vue.js.

Every node type has its own vue component located inside `view/adminhtml/web/vue/menu-type` directory.
(See `view/adminhtml/web/vue/menu-type/category.vue` or `view/adminhtml/web/vue/menu-type/cms-block.vue` examples for a reference)

UI initialization starts in `view/adminhtml/templates/menu/nodes.phtml` where we initialize `menu.js` and we pass list of paths of Vue components that are assigned to each node type using `"vueComponents"` property (see two fragments of code from `nodes.phtml` below).

```php
$vueComponents = $block->getVueComponents();
```

```js
<script type="text/x-magento-init">
    {
        "*": {
            "menuNodes": {
                "vueComponents": <?= json_encode($vueComponents) ?>,
                // ...
            }
        }
    }
</script>
```

To show new node in UI we need to add new Vue component via `di.xml`

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Snowdog\Menu\Model\VueProvider">
        <arguments>
            <argument name="components" xsi:type="array">
                <item name="component_name" xsi:type="string">component-file-name</item>
            </argument>
        </arguments>
    </type>
</config>
```

Where we need to define
- `component_name` - example: `cms_block`
- `component-file-name` -  example: `cms-block`

Then in `view/adminhtml/web/vue/menu-type/` we add `component-file-name.vue` ex. `cms-block.vue`

In new vue file we register our component (`component_name` ex. `cms_block`) and we add our logic we need.

```vue
<template>
    ...
</template>
<script>
    define(['Vue'], function(Vue) {
        Vue.component('component_name', {
        // example: Vue.component('cms_block', {
            ...
        }
    })
</script>
```

Newly created block with additional method should be added via `di.xml` defining block instance and node type code (code will be stored in database).

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Snowdog\Menu\Model\NodeTypeProvider">
        <arguments>
            <argument name="providers" xsi:type="array">
                <item name="my_node_type" xsi:type="object">Foo\Bar\Block\NodeType\MyNode</item>
            </argument>
        </arguments>
    </type>
</config>
```

`my_node_type` - example: `cms_block` (the same as `component_name`)
`Foo\Bar\Block\NodeType\MyNode` - example: `Snowdog\Menu\Block\NodeType\CmsBlock`

In our `cms_block` example it would be:
```xml
<item name="cms_block" xsi:type="object">Snowdog\Menu\Block\NodeType\CmsBlock</item>
```

### How to save data from vue components when saving menu changes?
When saving menu changes we send form post request that contains several fields like:
`form_key, id, title, identifier, css_class, stores[], serialized_notes`.

`serialized_notes` stores data from our vue components using computed property `jsonList`.

**App.vue:**
```html
<input
    type="hidden"
    name="serialized_nodes"
    :value="jsonList"
>
```

```js
computed: {
    jsonList: function() {
        return JSON.stringify(this.list);
    }
}
```

The `list` and `item` objects are passed from `App.vue` to child components.
As they are objects, they are passed by reference and editing them in child components, updates the value of `serialized_nodes` in `App.vue`.

This is not an ideal way of mutating data, and we plan to refactor it.

For now look at `menu-type.vue`. You can find:

```vue
<component
    :is="item['type']"
    :item="item"
    :config="config"
/>
```

This loads dynamically a component of a chosen type of node. For example for a node type: `cms_block` -> `cms-block.vue`

Cms block node type component uses `autocomplete.vue` input type component with prop item `:item="item"`. Once user makes some changes, the data is propagated up to the root `App.vue` component, stringified and saved in a hidden input.

## Nodes Custom Templates
This feature allows you to add custom templates to each menu node type and node submenu.  
And it allows to select the custom templates in menu admin edit page.

The custom templates override the default ones that are provided by the module.

### Adding Nodes Custom Templates
- Create a directory inside your theme files that will contain the custom templates with the following structure:
```
Snowdog_Menu  
  └─ templates
    └─ {menu_identifier}
      └─ menu
        └─ custom
          └─ {custom_templates_directories}
```

- `{menu_identifier}` is the identifier that you enter when you create a menu on menu admin page.
- `{custom_templates_directories}` is a list of container directories for the custom templates.
- The name of the custom templates container directory can be either a node type (Check [Available Node Types](#available-node-types)) or `sub_menu`.
- Once the custom templates container directories are ready, you have to add the custom templates `PHTML` files to them. (Template file name must not be a [node type](#available-node-types).)
- After that, you can proceed to your menu admin edit page to select the custom templates that you want to use for your nodes. (Check [Configuring Nodes Custom Templates](#configuring-nodes-custom-templates).)

### Configuring Nodes Custom Templates
After adding your custom templates, you can select the templates that you want to use for your menu nodes in menu admin edit page.

In menu admin edit page, the `Node template` field will contain a list of available node type custom templates.  
And the `Submenu template` field will contain a list of available submenu templates. (Submenu template applies to the child nodes of a node.)

## Available Node Types
- `category`
- `product`
- `cms_page`
- `cms_block`
- `custom_url`
- `category_child`
- `wrapper`

## Available endpoints: 
   
 * `/rest/V1/menus`: retrieves available menus
 * `/rest/V1/nodes`: retrieves nodes by menuId

## GraphQL

### List of available queries
- `snowdogMenus`: Returns a list of active menus filtered by the array argument `identifiers`.

Usage:
```
query SnowdogMenusExample {
  snowdogMenus(identifiers: ["foo", "bar"]) {
    items {
      menu_id              # Type: Int
      identifier           # Type: String
      title                # Type: String
      css_class            # Type: String
      creation_time        # Type: String
      update_time          # Type: String
    }
  }
}
```
- `snowdogMenuNodes`: Returns a list of active menu nodes filtered by the menu `identifier` argument.

Usage:
```
query SnowdogMenuNodesExample {
  snowdogMenuNodes(identifier: "foobar") {
    items {
      node_id                # Type: Int
      menu_id                # Type: Int
      type                   # Type: String
      content                # Type: String
      classes                # Type: String
      parent_id              # Type: Int
      position               # Type: Int
      level                  # Type: Int
      title                  # Type: String
      target                 # Type: Int (0 for "_self", 1 for "_blank")
      image                  # Type: String
      image_alt_text         # Type: String
      creation_time          # Type: String
      update_time            # Type: String
      additional_data        # Type: [String]
    }
  }
}
```

### Notes
- Queries HTTP method must be `GET` in order to cache their results.

## Frontend
We are not providing any CSS or JS, only basic HTML, which means this module is not out of box supported by any theme, you always need to write some custom code to get expected results or pick some ready to use theme / extension, built on top of this module.

### Themes:
- [Snowdog Alpaca theme](https://github.com/SnowdogApps/magento2-alpaca-theme)

### Extensions:
- [RedChamps Luma theme support](https://github.com/redchamps/snowdog-menu-luma-support)
