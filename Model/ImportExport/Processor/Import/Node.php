<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import;

use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

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

    public function createNodes(array $nodes, int $menuId, int $nodesLevel = 0, ?int $parentId = null): void
    {
        foreach ($nodes as $nodeData) {
            $node = $this->nodeFactory->create();
            $data = $this->dataProcessor->get($nodeData, $menuId, $nodesLevel, $parentId);

            $node->setData($data);
            $this->nodeRepository->save($node);

            if (isset($nodeData[ExtendedFields::NODES])) {
                $nodeId = $node->getId() ? (int) $node->getId() : null;
                $this->createNodes($nodeData[ExtendedFields::NODES], $menuId, $nodesLevel + 1, $nodeId);
            }
        }
    }

    public function validateImportData(array $data): void
    {
        $this->validator->validate($data);
    }
}
