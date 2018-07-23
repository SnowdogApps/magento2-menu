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

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getNodeId()
    {
        return $this->_getData(NodeInterface::NODE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setNodeId($nodeId)
    {
        return $this->setData(NodeInterface::NODE_ID, $nodeId);
    }

    /**
     * @inheritdoc
     */
    public function getMenuId()
    {
        return $this->_getData(NodeInterface::MENU_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMenuId($menuId)
    {
        return $this->setData(NodeInterface::MENU_ID, $menuId);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(NodeInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(NodeInterface::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->_getData(NodeInterface::CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        return $this->setData(NodeInterface::CONTENT, $content);
    }

    /**
     * @inheritdoc
     */
    public function getClasses()
    {
        return $this->_getData(NodeInterface::CLASSES);
    }

    /**
     * @inheritdoc
     */
    public function setClasses($classes)
    {
        return $this->setData(NodeInterface::CLASSES, $classes);
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return $this->_getData(NodeInterface::PARENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setParentId($parentId)
    {
        return $this->setData(NodeInterface::PARENT_ID, $parentId);
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->_getData(NodeInterface::POSITION);
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        return $this->setData(NodeInterface::POSITION, $position);
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->_getData(NodeInterface::LEVEL);
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        return $this->setData(NodeInterface::LEVEL, $level);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->_getData(NodeInterface::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        return $this->setData(NodeInterface::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getTarget()
    {
        return $this->_getData(NodeInterface::TARGET);
    }

    /**
     * @inheritdoc
     */
    public function setTarget($target)
    {
        return $this->setData(NodeInterface::TARGET, $target);
    }

    /**
     * @inheritdoc
     */
    public function getCreationTime()
    {
        return $this->_getData(NodeInterface::CREATION_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(NodeInterface::CREATION_TIME, $creationTime);
    }

    /**
     * @inheritdoc
     */
    public function getUpdateTime()
    {
        return $this->_getData(NodeInterface::UPDATE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(NodeInterface::UPDATE_TIME, $updateTime);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->_getData(NodeInterface::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(NodeInterface::IS_ACTIVE, $isActive);
    }
}
