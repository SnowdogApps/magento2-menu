<?php
namespace Snowdog\Menu\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Api\Data\MenuInterface;

/**
 * @method string getCssClass()
 */
class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{
    const CACHE_TAG = 'snowdog_menu_menu';

    protected $_cacheTag = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(\Snowdog\Menu\Model\ResourceModel\Menu::class);
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

    /**
     * @return bool
     */
    public function saveStores(array $stores)
    {
        if ($stores == $this->getStores()) {
            return false;
        }

        $connection = $this->getResource()->getConnection();
        $connection->beginTransaction();
        $table = $this->getResource()->getTable('snowmenu_store');
        $connection->delete($table, ['menu_id = ?' => $this->getId()]);
        foreach ($stores as $store) {
            $connection->insert($table, ['menu_id' => $this->getId(), 'store_id' => $store]);
        }
        $connection->commit();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getMenuId()
    {
        return $this->_getData(MenuInterface::MENU_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMenuId($menuId)
    {
        return $this->setData(MenuInterface::MENU_ID, $menuId);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->_getData(MenuInterface::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        return $this->setData(MenuInterface::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return $this->_getData(MenuInterface::IDENTIFIER);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(MenuInterface::IDENTIFIER, $identifier);
    }

    /**
     * @inheritdoc
     */
    public function getCreationTime()
    {
        return $this->_getData(MenuInterface::CREATION_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setCssClass($cssClass)
    {
        return $this->setData(MenuInterface::CSS_CLASS, $cssClass);
    }

    /**
     * @inheritdoc
     */
    public function getCssClass()
    {
        return $this->_getData(MenuInterface::CSS_CLASS);
    }

    /**
     * @inheritdoc
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(MenuInterface::CREATION_TIME, $creationTime);
    }

    /**
     * @inheritdoc
     */
    public function getUpdateTime()
    {
        return $this->_getData(MenuInterface::UPDATE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(MenuInterface::UPDATE_TIME, $updateTime);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->_getData(MenuInterface::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(MenuInterface::IS_ACTIVE, $isActive);
    }
}
