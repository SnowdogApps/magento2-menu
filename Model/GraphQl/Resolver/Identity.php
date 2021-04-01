<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver;

class Identity
{
    public function getIdentities(array $resolvedData, string $identifier, string $cacheTag): array
    {
        $ids = [];
        $items = $resolvedData['items'] ?? [];

        foreach ($items as $item) {
            if (is_array($item) && !empty($item[$identifier])) {
                $ids[] = $cacheTag . '_' . $item[$identifier];
            }
        }

        if ($ids) {
            array_unshift($ids, $cacheTag);
        }

        return $ids;
    }
}
