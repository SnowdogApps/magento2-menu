<?php
namespace Snowdog\Menu\Model\ResourceModel\Menu\Node;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\Menu\Node', 'Snowdog\Menu\Model\ResourceModel\Menu\Node');
    }
}
