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

    /**
     * Gets menu id.
     *
     * @return int|null
     */
    public function getMenuId()
    {
        return $this->_getData(MenuInterface::MENU_ID);
    }

    /**
     * Set menu id
     *
     * @param int $menuId
     * @return $this
     */
    public function setMenuId($menuId)
    {
        return $this->setData(MenuInterface::MENU_ID, $menuId);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData(MenuInterface::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(MenuInterface::TITLE, $title);
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_getData(MenuInterface::IDENTIFIER);
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(MenuInterface::IDENTIFIER, $identifier);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->_getData(MenuInterface::CREATION_TIME);
    }

    /**
     * Set css class
     *
     * @param string $cssClass
     * @return $this
     */
    public function setCssClass($cssClass)
    {
        return $this->setData(MenuInterface::CSS_CLASS, $cssClass);
    }

    /**
     * Get css class
     *
     * @return string
     */
    public function getCssClass()
    {
        return $this->_getData(MenuInterface::CSS_CLASS);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(MenuInterface::CREATION_TIME, $creationTime);
    }

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->_getData(MenuInterface::UPDATE_TIME);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(MenuInterface::UPDATE_TIME, $updateTime);
    }

    /**
     * Get is active
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->_getData(MenuInterface::IS_ACTIVE);
    }

    /**
     * Set is active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(MenuInterface::IS_ACTIVE, $isActive);
    }
}
