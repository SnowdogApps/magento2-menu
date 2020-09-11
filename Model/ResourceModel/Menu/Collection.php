<?php
namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'menu_id';

    protected function _construct()
    {
        $this->_init(
            \Snowdog\Menu\Model\Menu::class,
            \Snowdog\Menu\Model\ResourceModel\Menu::class
        );
    }
}
