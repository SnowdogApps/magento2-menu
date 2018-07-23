<?php

namespace Snowdog\Menu\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Search results interface.
 *
 * @api
 */
interface MenuSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return \Snowdog\Menu\Api\Data\MenuInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param \Snowdog\Menu\Api\Data\MenuInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
