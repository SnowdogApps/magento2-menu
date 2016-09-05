<?php

namespace Snowdog\Menu\Model;

class NodeTypeProvider
{
    /**
     * @var array
     */
    private $providers;

    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    public function prepareData($type, $nodes)
    {
        $this->providers[$type]->fetchData($nodes);
    }

    public function render($type, $id, $level)
    {
        return $this->providers[$type]->getHtml($id, $level);
    }
}