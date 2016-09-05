<?php
namespace Snowdog\Menu\Api;

use Snowdog\Menu\Api\Data\MenuInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface MenuRepositoryInterface 
{
    public function save(MenuInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(MenuInterface $page);

    public function deleteById($id);

    public function get(string $identifier, int $storeId);
}
