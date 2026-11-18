<?php
namespace Snowdog\Menu\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Snowdog\Menu\Model\ResourceModel\Menu as MenuResource;
use Magento\Framework\Model\AbstractModel;
use Snowdog\Menu\Api\Data\MenuInterface;

/**
 * @method string getCssClass()
 */
class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{
    public const CACHE_TAG = 'snowdog_menu_menu';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var MenuResource
     */
    private $menuResource;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param MenuResource $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        MenuResource $resource,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->menuResource = $resource;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Snowdog\Menu\Model\ResourceModel\Menu::class);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Return the store IDs assigned to the menu.
     */
    public function getStores()
    {
        $connection = $this->menuResource->getConnection();
        $select = $connection->select()->from($this->menuResource->getTable('snowmenu_store'), ['store_id'])->where(
            'menu_id = ?',
            $this->getId()
        );
        return $connection->fetchCol($select);
    }

    /**
     * Replace the assigned stores when the selection changes.
     *
     * @param array $stores
     * @return bool
     */
    public function saveStores(array $stores)
    {
        if ($stores == $this->getStores()) {
            return false;
        }

        $connection = $this->menuResource->getConnection();
        $connection->beginTransaction();
        $table = $this->menuResource->getTable('snowmenu_store');
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
