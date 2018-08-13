[![Packagist](https://img.shields.io/packagist/v/snowdog/module-menu.svg)](https://packagist.org/packages/snowdog/module-menu) [![Packagist](https://img.shields.io/packagist/dt/snowdog/module-menu.svg)](https://packagist.org/packages/snowdog/module-menu)

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
To add new type node you have to add new backend block that also implements `\Snowdog\Menu\Api\NodeTypeInterface`.

Backend block will be directly injected into menu editor.
Menu editor is using Vue.js so you need to create a new vue component that has node type code as name in `view/adminhtml/web/vue/menu-type` and load it in `view/adminhtml/templates/menu/nodes.phtml`
(See `view/adminhtml/web/vue/menu-type/category.vue` for reference)

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

## Available endpoints: 
   
 * `/rest/V1/menus`: retrieves available menus
 * `/rest/V1/nodes`: retrieves nodes by menuId
 