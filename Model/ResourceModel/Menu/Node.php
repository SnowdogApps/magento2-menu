<?php
namespace Snowdog\Menu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Node extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('snowmenu_node', 'node_id');
    }
}
