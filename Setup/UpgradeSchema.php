<?php

namespace Snowdog\Menu\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->addMenuCssClassField($setup);
        }
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
}
