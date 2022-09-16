<?php

declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;

class Nodes
{
    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;

    /**
     * @var FilterGroupBuilderFactory
     */
    private $filterGroupBuilderFactory;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    public function __construct(
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        NodeRepositoryInterface $nodeRepository,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->nodeRepository = $nodeRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    public function getList(MenuInterface $menu): array
    {
        $filterBuilder = $this->filterBuilderFactory->create();
        $filter = $filterBuilder->setField('menu_id')
            ->setValue($menu->getMenuId())
            ->setConditionType('eq')
            ->create();

        $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
        $filterGroup = $filterGroupBuilder->addFilter($filter)->create();
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder->setAscendingDirection()->setField('level')->create()
        );
        $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

        return $this->nodeRepository->getList($searchCriteria)->getItems();
    }
}
