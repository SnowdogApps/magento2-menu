<?php
namespace Snowdog\Menu\Api;

use Snowdog\Menu\Api\Data\NodeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NodeRepositoryInterface
{
    public function save(NodeInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(NodeInterface $page);

    public function deleteById($id);

    public function getByMenu($menuId);
}
