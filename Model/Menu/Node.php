<?php
namespace Snowdog\Menu\Model\Menu;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Api\Data\NodeInterface;

class Node extends AbstractModel implements NodeInterface, IdentityInterface
{
    const CACHE_TAG = 'snowdog_menu_node';

    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\Menu\Node');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
