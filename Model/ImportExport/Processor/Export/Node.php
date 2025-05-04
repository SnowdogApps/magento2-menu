<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\Tree as NodeTree;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\DataProcessor;

class Node
{
    const EXCLUDED_FIELDS = [
        NodeInterface::MENU_ID,
        NodeInterface::NODE_ID,
        NodeInterface::PARENT_ID,
        NodeInterface::LEVEL
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var NodeTree
     */
    private $nodeTree;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        NodeRepositoryInterface $nodeRepository,
        NodeTree $nodeTree,
        DataProcessor $dataProcessor
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTree = $nodeTree;
        $this->dataProcessor = $dataProcessor;
    }

    public function getList(int $menuId): array
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(NodeInterface::LEVEL)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(NodeInterface::MENU_ID, $menuId)
            ->setSortOrders([$sortOrder])
            ->create();

        $nodes = $this->nodeRepository->getList($searchCriteria)->getItems();

        if (!empty($nodes)) {
            $this->dataProcessor->preloadTranslations($nodes);
        }

        return $nodes ? $this->nodeTree->get($nodes) : [];
    }
}
