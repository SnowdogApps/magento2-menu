<?php

namespace Snowdog\Menu\Model;

use Snowdog\Menu\Api\Data\NodeTypeInterface;

class NodeTypeProvider
{
    /**
     * @var array
     */
    private $providers;

    /**
     * @var array
     */
    private $typeModels;

    /**
     * NodeTypeProvider constructor.
     *
     * @param array $providers
     * @param array $typeModels
     */
    public function __construct(array $providers = [], array $typeModels = [])
    {
        $this->providers = $providers;
        $this->typeModels = $typeModels;
    }

    /**
     * @param $type
     * @param $nodes
     */
    public function prepareData($type, $nodes)
    {
        $this->providers[$type]->fetchData($nodes);
    }

    /**
     * @param string $type
     * @return \Snowdog\Menu\Api\NodeTypeInterface
     */
    public function getProvider($type)
    {
        return $this->providers[$type];
    }

    public function getTypeModel(string $type): NodeTypeInterface
    {
        return $this->typeModels[$type];
    }

    /**
     * @param $type
     * @param $id
     * @param $level
     *
     * @return mixed
     */
    public function render($type, $id, $level)
    {
        return $this->providers[$type]->getHtml($id, $level);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        $result = [];
        foreach ($this->providers as $code => $instance) {
            $result[$code] = $instance->getLabel();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getEditForms()
    {
        return $this->providers;
    }
}
