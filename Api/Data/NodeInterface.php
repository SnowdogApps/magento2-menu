<?php
namespace Snowdog\Menu\Api\Data;

interface NodeInterface
{
    /**
     * Constants for field names.
     */
    const NODE_ID = 'node_id';
    const MENU_ID = 'menu_id';
    const TYPE = 'type';
    const CONTENT = 'content';
    const CLASSES = 'classes';
    const PARENT_ID = 'parent_id';
    const POSITION = 'position';
    const LEVEL = 'level';
    const TITLE = 'title';
    const TARGET = 'target';
    const IMAGE = 'image';
    const IMAGE_ALT_TEXT = 'image_alt_text';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const IS_ACTIVE = 'is_active';
    const ADDITIONAL_DATA = 'additional_data';
    const SELECTED_ITEM_ID = 'selected_item_id';

    /**
     * Get node id
     *
     * @return int
     */
    public function getNodeId();

    /**
     * Set node id
     *
     * @param int $nodeId
     * @return $this
     */
    public function setNodeId($nodeId);

    /**
     * Get menu id
     *
     * @return int
     */
    public function getMenuId();

    /**
     * Set menu id
     *
     * @param int $menuId
     * @return $this
     */
    public function setMenuId($menuId);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get classes
     *
     * @return string
     */
    public function getClasses();

    /**
     * Set classes
     *
     * @param string $classes
     * @return $this
     */
    public function setClasses($classes);

    /**
     * Get parent id
     *
     * @return int
     */
    public function getParentId();

    /**
     * Set parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel();

    /**
     * Set level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get target
     *
     * @return int
     */
    public function getTarget();

    /**
     * Set target
     *
     * @param int $target
     * @return $this
     */
    public function setTarget($target);

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get image alt text
     *
     * @return string
     */
    public function getImageAltText();

    /**
     * Set image alt text
     *
     * @param string $altText
     * @return $this
     */
    public function setImageAltText($altText);

    /**
     * Get creation time
     *
     * @return string
     */
    public function getCreationTime();

    /**
     * Set creation time
     *
     * @param string $creationTime format: Y-m-d H:i:s
     * @return $this
     */
    public function setCreationTime($creationTime);

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime();

    /**
     * Set updated time
     *
     * @param string $updateTime format: Y-m-d H:i:s
     * @return $this
     */
    public function setUpdateTime($updateTime);

    /**
     * Get is active
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get additional data
     *
     * @return mixed[]
     */
    public function getAdditionalData();

    /**
     * Set additional data
     *
     * @param mixed[] $data
     * @return $this
     */
    public function setAdditionalData($data);

    /**
     * @return int
     */
    public function getSelectedItemId();

    /**
     * @param int $selectedItemId
     * @return $this
     */
    public function setSelectedItemId($selectedItemId);
}
