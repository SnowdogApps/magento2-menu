<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import;

use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\DataProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator\TreeTrace;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\TranslationProcessor;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;
use Snowdog\Menu\Model\ImportExport\File\Yaml;

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
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var TreeTrace
     */
    private $treeTrace;

    /**
     * @var Yaml
     */
    private $yaml;

    /**
     * @var TranslationProcessor
     */
    private $translationProcessor;

    public function __construct(
        NodeInterfaceFactory $nodeFactory,
        NodeRepositoryInterface $nodeRepository,
        DataProcessor $dataProcessor,
        Validator $validator,
        TreeTrace $treeTrace,
        Yaml $yaml,
        TranslationProcessor $translationProcessor
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->dataProcessor = $dataProcessor;
        $this->validator = $validator;
        $this->treeTrace = $treeTrace;
        $this->yaml = $yaml;
        $this->translationProcessor = $translationProcessor;
    }

    public function createNodes(
        array $nodes,
        int $menuId,
        int $level = 0,
        int $position = 0,
        ?int $parentId = null
    ): void {
        foreach ($nodes as $nodeData) {
            if ($nodeData === null) {
                continue;
            }

            $node = $this->nodeFactory->create();
            $data = $this->dataProcessor->getData($nodeData, $menuId, $level, $position++, $parentId);

            // Extract translations before saving node
            $translations = $nodeData[ExtendedFields::TRANSLATIONS] ?? [];
            unset($data[ExtendedFields::TRANSLATIONS]);

            $node->setData($data);
            $this->nodeRepository->save($node);

            // Process translations after node is saved
            if (!empty($translations)) {
                $this->translationProcessor->processTranslations((int)$node->getId(), $translations);
            }

            if (isset($nodeData[ExtendedFields::NODES])) {
                $nodeId = $node->getId() ? (int)$node->getId() : null;
                $this->createNodes($nodeData[ExtendedFields::NODES], $menuId, ($level + 1), 0, $nodeId);
            }
        }
    }

    public function validateImportData(array $data): void
    {
        if ($this->yaml->isHashArray($data)) {
            $this->treeTrace->disableNodeIdAddend();
        }

        $this->validator->validate($data);
    }
}
