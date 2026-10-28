<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Export\Node;

use Snowdog\Menu\Api\NodeTranslationRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class TranslationProcessor
{
    private NodeTranslationRepositoryInterface $nodeTranslationRepository;
    private StoreManagerInterface $storeManager;
    private array $translationsCache = [];

    public function __construct(
        NodeTranslationRepositoryInterface $nodeTranslationRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->nodeTranslationRepository = $nodeTranslationRepository;
        $this->storeManager = $storeManager;
    }

    public function preloadTranslations(array $nodeIds): void
    {
        if (empty($nodeIds)) {
            return;
        }

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $translations = $this->nodeTranslationRepository->getByNodeIds($nodeIds, (int)$store->getId());
            foreach ($translations as $translation) {
                $nodeId = $translation->getNodeId();
                if ($translation->getTitle()) {
                    $this->translationsCache[$nodeId][$store->getCode()] = $translation->getTitle();
                }
            }
        }
    }

    public function getTranslations(int $nodeId): array
    {
        return $this->translationsCache[$nodeId] ?? [];
    }
}
