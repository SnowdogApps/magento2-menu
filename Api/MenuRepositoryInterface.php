<?php
namespace Snowdog\Menu\Api;

use Snowdog\Menu\Api\Data\MenuInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface MenuRepositoryInterface 
{
    /**
     * @param \Snowdog\Menu\Api\Data\MenuInterface $page
     * @return \Snowdog\Menu\Api\Data\MenuInterface
     */
    public function save(MenuInterface $page);

    /**
     * @param int $id
     * @return \Snowdog\Menu\Model\Menu
     */
    public function getById($id);

    /**
     * Returns menus list
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Snowdog\Menu\Api\Data\MenuSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param \Snowdog\Menu\Api\Data\MenuInterface $page
     * @return bool
     */
    public function delete(MenuInterface $page);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id);

    /**
     * @param string $identifier
     * @param int $storeId
     * @return \Snowdog\Menu\Model\Menu
     */
    public function get($identifier, $storeId);
}
