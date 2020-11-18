<?php

namespace Snowdog\Menu\Model\Menu\ImportProcessor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Model\Menu\ExportProcessor;

class Menu
{
    const REQUIRED_FIELDS = [
        MenuInterface::TITLE,
        MenuInterface::IDENTIFIER,
        MenuInterface::CSS_CLASS,
        MenuInterface::IS_ACTIVE,
        ExportProcessor::STORES_CSV_FIELD
    ];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MenuInterfaceFactory $menuFactory,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->menuFactory = $menuFactory;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @return MenuInterface
     */
    public function createMenu(array $data, array $stores)
    {
        $menu = $this->menuFactory->create();

        $menu->setData($this->getProcessedMenuData($data));
        $this->menuRepository->save($menu);
        $menu->saveStores($stores);

        return $menu;
    }

    /**
     * @throws ValidatorException
     */
    public function validateImportData(array $data)
    {
        $missingFields = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            throw new ValidatorException(
                __('The following menu required import fields are missing: "%1".', implode('", "', $missingFields))
            );
        }
    }

    /**
     * @param string $identifier
     * @return string
     */
    private function getNewMenuIdentifier($identifier)
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

    /**
     * @param string $identifier
     * @return array
     */
    private function getMenuListByIdentifier($identifier)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(MenuInterface::IDENTIFIER, "${identifier}%", 'like')
            ->create();

        return $this->menuRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @return array
     */
    private function getProcessedMenuData(array $data)
    {
        $data[MenuInterface::IDENTIFIER] = $this->getNewMenuIdentifier($data[MenuInterface::IDENTIFIER]);

        if (isset($data[MenuInterface::IS_ACTIVE])) {
            $data[MenuInterface::IS_ACTIVE] = (bool) $data[MenuInterface::IS_ACTIVE];
        }

        return $data;
    }
}
