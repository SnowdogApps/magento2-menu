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
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get node id
     *
     * @return int
     */
    public function getNodeId()
    {
        return $this->_getData(NodeInterface::NODE_ID);
    }

    /**
     * Set node id
     *
     * @param int $nodeId
     * @return $this
     */
    public function setNodeId($nodeId)
    {
        return $this->setData(NodeInterface::NODE_ID, $nodeId);
    }

    /**
     * Get menu id
     *
     * @return int
     */
    public function getMenuId()
    {
        return $this->_getData(NodeInterface::MENU_ID);
    }

    /**
     * Set menu id
     *
     * @param int $menuId
     * @return $this
     */
    public function setMenuId($menuId)
    {
        return $this->setData(NodeInterface::MENU_ID, $menuId);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData(NodeInterface::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(NodeInterface::TYPE, $type);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_getData(NodeInterface::CONTENT);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        return $this->setData(NodeInterface::CONTENT, $content);
    }

    /**
     * Get classes
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->_getData(NodeInterface::CLASSES);
    }

    /**
     * Set classes
     *
     * @param string $classes
     * @return $this
     */
    public function setClasses($classes)
    {
        return $this->setData(NodeInterface::CLASSES, $classes);
    }

    /**
     * Get parent id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_getData(NodeInterface::PARENT_ID);
    }

    /**
     * Set parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData(NodeInterface::PARENT_ID, $parentId);
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_getData(NodeInterface::POSITION);
    }

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->setData(NodeInterface::POSITION, $position);
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->_getData(NodeInterface::LEVEL);
    }

    /**
     * Set level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        return $this->setData(NodeInterface::LEVEL, $level);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData(NodeInterface::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(NodeInterface::TITLE, $title);
    }

    /**
     * Get target
     *
     * @return int
     */
    public function getTarget()
    {
        return $this->_getData(NodeInterface::TARGET);
    }

    /**
     * Set target
     *
     * @param int $target
     * @return $this
     */
    public function setTarget($target)
    {
        return $this->setData(NodeInterface::TARGET, $target);
    }

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->_getData(NodeInterface::CREATION_TIME);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(NodeInterface::CREATION_TIME, $creationTime);
    }

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->_getData(NodeInterface::UPDATE_TIME);
    }

    /**
     * Set updated time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(NodeInterface::UPDATE_TIME, $updateTime);
    }

    /**
     * Get is active
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->_getData(NodeInterface::IS_ACTIVE);
    }

    /**
     * Set is active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(NodeInterface::IS_ACTIVE, $isActive);
    }
}
