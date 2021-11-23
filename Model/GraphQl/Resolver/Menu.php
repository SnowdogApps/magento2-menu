<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Menu as MenuDataProvider;

class Menu implements ResolverInterface
{
    /**
     * @var MenuDataProvider
     */
    private $dataProvider;

    public function __construct(MenuDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
        $identifiers = $this->getIdentifiers($args);

        return ['items' => $this->getData($identifiers, $storeId)];
    }

    private function getIdentifiers(array $args): array
    {
        if (!isset($args['identifiers'])) {
            return [];
        }

        return array_unique(array_filter($args['identifiers']));
    }

    private function getData(array $identifiers, int $storeId): array
    {
        $data = $this->dataProvider->getList($identifiers, $storeId);

        foreach ($identifiers as $identifier) {
            if (!isset($data[$identifier])) {
                $data[$identifier] = new GraphQlNoSuchEntityException(
                    __('Could not find a menu with identifier "%1".', $identifier)
                );
            }
        }

        return $data;
    }
}
