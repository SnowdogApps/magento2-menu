<?php

namespace Snowdog\Menu\Model\Menu\ImportProcessor;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Serialize\SerializerInterface;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\NodeTypeProvider;

class Node
{
    const REQUIRED_FIELDS = [
        NodeInterface::TYPE
    ];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var NodeInterfaceFactory
     */
    private $nodeFactory;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    public function __construct(
        SerializerInterface $serializer,
        NodeInterfaceFactory $nodeFactory,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider
    ) {
        $this->serializer = $serializer;
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
    }

    /**
     * @param int $menuId
     */
    public function createNodes(array $nodes, $menuId)
    {
        $nodesIds = [];

        foreach ($nodes as $nodeData) {
            $node = $this->nodeFactory->create();
            $processedNodeData = $this->getProcessedNodeData($nodeData, $menuId, $nodesIds);

            $node->setData($processedNodeData);
            $this->nodeRepository->save($node);

            if (isset($nodeData[NodeInterface::NODE_ID])) {
                $nodesIds[$nodeData[NodeInterface::NODE_ID]] = $node->getId();
            }
        }
    }

    /**
     * @throws ValidatorException
     */
    public function validateImportData(array $data)
    {
        $nodeTypes = array_keys($this->nodeTypeProvider->getLabels());

        foreach ($data as $nodeNumber => $node) {
            $missingFields = [];
            $nodeNumber++;

            foreach (self::REQUIRED_FIELDS as $field) {
                if (empty($node[$field])) {
                    $missingFields[] = $field;
                }
            }

            if ($missingFields) {
                throw new ValidatorException(
                    __(
                        'The following node %1 required import fields are missing: %2.',
                        $nodeNumber,
                        implode(', ', $missingFields)
                    )
                );
            }

            if (!in_array($node[NodeInterface::TYPE], $nodeTypes)) {
                throw new ValidatorException(__('Node $1 type is invalid.', $nodeNumber));
            }
        }
    }

    /**
     * @param string $data
     * @throws ValidatorException
     * @return array
     */
    public function getNodesJsonData($data)
    {
        try {
            return $this->serializer->unserialize($data);
        } catch (\InvalidArgumentException $exception) {
            throw new ValidatorException(__('Invalid menu nodes JSON format.'));
        }
    }

    /**
     * @param int $menuId
     * @return array
     */
    private function getProcessedNodeData(array $data, $menuId, array $nodesIds)
    {
        $data[NodeInterface::MENU_ID] = $menuId;

        if (isset($data[NodeInterface::PARENT_ID])) {
            $data[NodeInterface::PARENT_ID] = $nodesIds[$data[NodeInterface::PARENT_ID]] ?? null;
        }

        if (empty($data[NodeInterface::PARENT_ID])) {
            $data[NodeInterface::LEVEL] = 0;
        }

        if (isset($data[NodeInterface::TARGET])) {
            $data[NodeInterface::TARGET] = (bool) $data[NodeInterface::TARGET];
        }

        if (isset($data[NodeInterface::IS_ACTIVE])) {
            $data[NodeInterface::IS_ACTIVE] = (bool) $data[NodeInterface::IS_ACTIVE];
        }

        unset($data[NodeInterface::NODE_ID]);

        return $data;
    }
}
