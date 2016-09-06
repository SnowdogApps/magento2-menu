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

    public function getAddButtonLabels()
    {
        $result = [];
        foreach ($this->providers as $code => $instance) {
            $result[$code] = $instance->getAddButtonLabel();
        }
        return $result;
    }

    public function getEditForms()
    {
        return $this->providers;
    }
}