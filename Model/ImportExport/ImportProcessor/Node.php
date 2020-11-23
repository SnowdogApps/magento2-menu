<?php

namespace Snowdog\Menu\Model\ImportExport\ImportProcessor;

use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\ExportProcessor;

class Node
{
    /**
     * @var NodeInterfaceFactory
     */
    private $nodeFactory;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var Node\DataProcessor
     */
    private $dataProcessor;

    /**
     * @var Node\Validator
     */
    private $validator;

    public function __construct(
        NodeInterfaceFactory $nodeFactory,
        NodeRepositoryInterface $nodeRepository,
        Node\DataProcessor $dataProcessor,
        Node\Validator $validator
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->dataProcessor = $dataProcessor;
        $this->validator = $validator;
    }

    /**
     * @param int $menuId
     * @param int $nodesLevel
     * @param int|null $parentNode
     */
    public function createNodes(array $nodes, $menuId, $nodesLevel = 0, $parentNode = null)
    {
        foreach ($nodes as $nodeData) {
            $node = $this->nodeFactory->create();
            $data = $this->dataProcessor->get($nodeData, $menuId, $nodesLevel, $parentNode);

            $node->setData($data);
            $this->nodeRepository->save($node);

            if (isset($nodeData[ExportProcessor::NODES_FIELD])) {
                $this->createNodes($nodeData[ExportProcessor::NODES_FIELD], $menuId, $nodesLevel + 1, $node->getId());
            }
        }
    }

    public function validateImportData(array $data)
    {
        $this->validator->validate($data);
    }
}
