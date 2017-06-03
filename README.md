# Magento 2 Menu

Provides powerful menu editor to replace category based menus in Magento 2.

## Use

List of menus is located in Admin Panel under `Content > Elements > Menus`.

Following is example how to replace main menu with user defined menu (with identifier `main`).

```xml
<referenceBlock name="catalog.topnav" remove="true"/>
<referenceContainer name="store.menu">
  <block name="main.menu" class="Snowdog\Menu\Block\Menu" template="Snowdog_Menu::menu.phtml">
     <arguments>
        <argument name="menu" xsi:type="string">main</argument>
     </arguments>
  </block>
</referenceContainer>
```

## Adding new types of nodes

To add new type node You have to add new backend block that also implements `\Snowdog\Menu\Api\NodeTypeInterface`.

Backend block will be directly injected into menu editor.
Note that only one instance of block will be rendered and You should handle showing and hiding of this block in javascript depending on selected type of node.
(See `view/adminhtml/web/js/category.js` and  `view/adminhtml/templates/menu/node_type/category.phtml` for reference)

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
