<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            $this->changeTitleType($setup);
        }

        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            $this->addMenuCssClassField($setup);
        }

        if (version_compare($context->getVersion(), '0.2.1', '<')) {
            $this->addTargetAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.2.2', '<')) {
            $this->updateTargetAttribute($setup);
        }

        if (version_compare($context->getVersion(), '0.2.3', '<')) {
            $this->addForeignKeys($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addMenuCssClassField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('snowmenu_menu'),
            'css_class',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'after' => 'identifier',
                'default' => 'menu',
                'comment' => 'CSS Class'
            ]
        );

        return $this;
    }

    private function changeTitleType(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('snowmenu_node'),
            'title',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false
            ],
            'Demo Title'
        );
    }

    private function addTargetAttribute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('snowmenu_node'),
            'target',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 10,
                'nullable' => true,
                'after' => 'title',
                'default' => '_self',
                'comment' => 'Link target',
            ]
        );

        return $this;
    }

    private function updateTargetAttribute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('snowmenu_node');
        $connection = $setup->getConnection();

        $connection->update(
            $table,
            ['target' => 0],
            "target = '_self'"
        );
        $connection->update(
            $table,
            ['target' => 1],
            "target = '_blank'"
        );
        $connection->modifyColumn(
            $table,
            'target',
            [
                'type' => Table::TYPE_BOOLEAN,
                'default' => 0,
            ]
        );
    }

    private function addForeignKeys(SchemaSetupInterface $setup)
    {
        $menuTable = $setup->getTable('snowmenu_menu');
        $nodeTable = $setup->getTable('snowmenu_node');
        $storeTable = $setup->getTable('snowmenu_store');
        $setup->getConnection()->modifyColumn(
            $nodeTable,
            'menu_id',
            [
                'type' => Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => false,
                'unsigned' => true,
                'comment' => 'Menu ID'
            ]
        );

        $setup->getConnection()->modifyColumn(
            $storeTable,
            'store_id',
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => 5,
                'nullable' => false,
                'primary' => true, 
                'unsigned' => true,
                'comment' => 'Store ID'
            ]
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'snowmenu_node', 
                'menu_id', 
                'snowmenu_menu', 
                'menu_id'
            ),
            $nodeTable,
            'menu_id',
            $menuTable,
            'menu_id',
            Table::ACTION_CASCADE
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'snowmenu_store', 
                'menu_id', 
                'snowmenu_menu', 
                'menu_id'
            ),
            $storeTable,
            'menu_id',
            $menuTable,
            'menu_id',
            Table::ACTION_CASCADE
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                'snowmenu_store', 
                'store_id', 
                'store', 
                'store_id'
            ),
            $storeTable,
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        );
    }
}
