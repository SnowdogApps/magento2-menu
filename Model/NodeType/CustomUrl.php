<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model\NodeType;

class CustomUrl extends AbstractNode
{
    /**
     * @inheritDoc
     */
    public function fetchConfigData()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchData(array $nodes, $storeId)
    {
        $this->profiler->start(__METHOD__);

        $localNodes = [];

        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
        }

        $this->profiler->stop(__METHOD__);

        return $localNodes;
    }
}