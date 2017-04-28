<?php

namespace Snowdog\Menu\Model;

class NodeTypeProvider
{
    /**
     * @var array
     */
    private $providers;

    /**
     * NodeTypeProvider constructor.
     *
     * @param array $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
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
    public function getAddButtonLabels()
    {
        $result = [];
        foreach ($this->providers as $code => $instance) {
            $result[$code] = $instance->getAddButtonLabel();
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
