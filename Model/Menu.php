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

    public function getStores()
    {
        $connection = $this->getResource()->getConnection();
        $select = $connection->select()->from($this->getResource()->getTable('snowmenu_store'), ['store_id'])->where(
            'menu_id = ?',
            $this->getId()
        );
        return $connection->fetchCol($select);
    }

    public function saveStores(array $stores)
    {
        $connection = $this->getResource()->getConnection();
        $connection->beginTransaction();
        $table = $this->getResource()->getTable('snowmenu_store');
        $connection->delete($table, ['menu_id = ?' => $this->getId()]);
        foreach ($stores as $store) {
            $connection->insert($table, ['menu_id' => $this->getId(), 'store_id' => $store]);
        }
        $connection->commit();
    }
}
