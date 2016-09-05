<?php
namespace Snowdog\Menu\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('snowmenu_menu')
        )->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Demo Title'
        )->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Menu identifier'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('snowmenu_node')
        )->addColumn(
            'node_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Node ID'
        )->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Menu ID'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Node Type'
        )->addColumn(
            'content',
            Table::TYPE_TEXT,
            null,
            [],
            'Node contents'
        )->addColumn(
            'classes',
            Table::TYPE_TEXT,
            255,
            [],
            'CSS class name'
        )->addColumn(
            'parent_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Parent Node ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Node position'
        )->addColumn(
            'level',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Node level'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Demo Title'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('snowmenu_store')
        )->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Menu ID'
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Store ID'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
