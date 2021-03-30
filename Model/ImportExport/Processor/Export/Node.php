<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\Tree as NodeTree;

class Node
{
    const EXCLUDED_FIELDS = [
        NodeInterface::MENU_ID,
        NodeInterface::NODE_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL,
        NodeInterface::POSITION
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var NodeTree
     */
    private $nodeTree;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        NodeRepositoryInterface $nodeRepository,
        NodeTree $nodeTree
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTree = $nodeTree;
    }

    public function getList(int $menuId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(NodeInterface::MENU_ID, $menuId)
            ->create();

        $nodes = $this->nodeRepository->getList($searchCriteria)->getItems();

        return $nodes ? $this->nodeTree->get($nodes) : [];
    }
}
