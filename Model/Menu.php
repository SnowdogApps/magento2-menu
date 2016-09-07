<?php
namespace Snowdog\Menu\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Api\Data\MenuInterface;

class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{
    const CACHE_TAG = 'snowdog_menu_menu';

    protected $_cacheTag = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init('Snowdog\Menu\Model\ResourceModel\Menu');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}
