<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Api\Data;

interface NodeTypeInterface
{
    /**
     * Fetch additional data required for rendering nodes.
     *
     * @param array $nodes
     * @param int|string $storeId
     *
     * @return mixed
     */
    public function fetchData(array $nodes, $storeId);

    /**
     * Fetch additional data required for config.
     *
     * @return mixed
     */
    public function fetchConfigData();
}