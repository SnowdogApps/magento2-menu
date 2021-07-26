<?php

declare(strict_types=1);

namespace Snowdog\Menu\Api;

/**
 * @api
 */
interface MenuManagementInterface
{
    /**
     * Retrieve list of categories
     *
     * @param int|null $rootCategoryId
     * @param int|null $depth
     * @return array
     */
    public function getCategoryNodeList($rootCategoryId = null, $depth = null): array;
}
