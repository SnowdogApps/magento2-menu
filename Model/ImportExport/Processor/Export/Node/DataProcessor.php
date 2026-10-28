<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node;

use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Export\Node\TypeContent;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class DataProcessor
{
    /**
     * @var TypeContent
     */
    private $typeContent;
    private TranslationProcessor $translationProcessor;

    public function __construct(
        TypeContent $typeContent,
        TranslationProcessor $translationProcessor
    ) {
        $this->typeContent = $typeContent;
        $this->translationProcessor = $translationProcessor;
    }

    public function preloadTranslations(array $nodes): void
    {
        $nodeIds = array_map(
            fn($node) => (int)$node->getId(),
            $nodes
        );
        $this->translationProcessor->preloadTranslations($nodeIds);
    }

    public function getData(array $data): array
    {
        $data[NodeInterface::CONTENT] = $this->typeContent->get(
            $data[NodeInterface::TYPE],
            $data[NodeInterface::CONTENT]
        );

        $translations = $this->translationProcessor->getTranslations(
            (int)$data[NodeInterface::NODE_ID]
        );

        if (!empty($translations)) {
            $data[ExtendedFields::TRANSLATIONS] = $translations;
        }

        return $data;
    }
}
