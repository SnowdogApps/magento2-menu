<?php

namespace Snowdog\Menu\Model;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Snowdog\Menu\Api\Data\NodeInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * NodeTypeProvider constructor.
     *
     * @param array $providers
     * @param array $typeModels
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $providers = [], array $typeModels = [], ?LoggerInterface $logger = null)
    {
        $this->providers = $providers;
        $this->typeModels = $typeModels;
        $this->logger = $logger ?? new NullLogger();
    }

    public function isProviderRegistered(NodeInterface $node): bool
    {
        return $this->isRegistered($this->providers, $node);
    }

    public function isTypeModelRegistered(NodeInterface $node): bool
    {
        return $this->isRegistered($this->typeModels, $node);
    }

    private function isRegistered(array $registry, NodeInterface $node): bool
    {
        if (isset($registry[(string) $node->getType()])) {
            return true;
        }

        $this->logger->warning(
            'Menu node type is not registered',
            ['node_id' => $node->getNodeId(), 'menu_id' => $node->getMenuId(), 'type' => $node->getType()]
        );

        return false;
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
