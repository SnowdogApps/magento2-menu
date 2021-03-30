<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Menu;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;

class Identifier
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->menuRepository = $menuRepository;
    }

    public function getNewIdentifier(string $identifier): string
    {
        $menus = $this->getMenuListByIdentifier($identifier);
        if (!$menus) {
            return $identifier;
        }

        $identifiers = [];
        foreach ($menus as $menu) {
            $identifiers[$menu->getIdentifier()] = $menu->getId();
        }

        $idNumber = 1;
        $newIdentifier = $identifier . '-' . $idNumber;

        while (isset($identifiers[$newIdentifier])) {
            $newIdentifier = $identifier . '-' . (++$idNumber);
        }

        return $newIdentifier;
    }

    private function getMenuListByIdentifier(string $identifier): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(MenuInterface::IDENTIFIER, "${identifier}%", 'like')
            ->create();

        return $this->menuRepository->getList($searchCriteria)->getItems();
    }
}
