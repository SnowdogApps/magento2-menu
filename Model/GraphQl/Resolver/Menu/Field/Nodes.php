<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\Menu\Field;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Nodes implements ResolverInterface
{
    /**
     * @var ResolverInterface
     */
    private $nodeResolver;

    public function __construct(ResolverInterface $nodeResolver)
    {
        $this->nodeResolver = $nodeResolver;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        if ($args === null) {
            $args = [];
        }

        $args['identifier'] = $value['identifier'];

        return $this->nodeResolver->resolve($field, $context, $info, $value, $args);
    }
}
