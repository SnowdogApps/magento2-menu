<?php
namespace Snowdog\Menu\Api;

use Snowdog\Menu\Api\Data\NodeInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NodeRepositoryInterface
{
    /**
     * @param \Snowdog\Menu\Api\Data\NodeInterface $page
     * @return \Snowdog\Menu\Api\Data\NodeInterface
     */
    public function save(NodeInterface $page);

    /**
     * @param int $id
     * @return \Snowdog\Menu\Model\Menu\Node
     */
    public function getById($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * @param \Snowdog\Menu\Api\Data\NodeInterface $page
     * @return bool
     */
    public function delete(NodeInterface $page);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id);

    /**
     * Return node by menu id
     *
     * @api
     * @param int $menuId
     * @return \Snowdog\Menu\Api\Data\NodeInterface[]
     */
    public function getByMenu($menuId);

    /**
     * Return node by identifier
     *
     * @api
     * @param string $identifier
     * @return \Snowdog\Menu\Api\Data\NodeInterface[]
     */
    public function getByIdentifier($identifier);
}
