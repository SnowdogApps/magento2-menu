<?php
namespace Snowdog\Menu\Api\Data;

interface MenuInterface
{
    /**
     * Constants for field names.
     */
    const MENU_ID = 'menu_id';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const CSS_CLASS = 'css_class';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const IS_ACTIVE = 'is_active';
    const STORE_ID = 'store_id';

    const STORE_RELATION_TABLE = 'snowmenu_store';

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
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier);

    /**
     * Get css class
     *
     * @return string
     */
    public function getCssClass();

    /**
     * Set css class
     *
     * @param string $cssClass
     * @return $this
     */
    public function setCssClass($cssClass);

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
     * Set update time
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
}
