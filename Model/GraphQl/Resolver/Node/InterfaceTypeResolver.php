<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\Node;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\Entity\MapperInterface;

class InterfaceTypeResolver implements TypeResolverInterface
{
    const ENTITY_TYPE = 'snowdog_menu_node';
    const DEFAULT_TYPE = 'SnowdogMenuNode';

    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @inheritDoc
     */
    public function resolveType(array $data): string
    {
        if (!isset($data['type'])) {
            throw new GraphQlInputException(__('Missing key "type" in node data.'));
        }

        $mappedTypes = $this->mapper->getMappedTypes(self::ENTITY_TYPE);

        return $mappedTypes[$data['type']] ?? self::DEFAULT_TYPE;
    }
}
