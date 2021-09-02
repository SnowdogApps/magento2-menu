<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\NodeType;

class CategoryChild extends AbstractNode
{
    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        return [];
    }
}
