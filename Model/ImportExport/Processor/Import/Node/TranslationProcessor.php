<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node;

use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Api\NodeTranslationRepositoryInterface;
use Snowdog\Menu\Api\Data\NodeTranslationInterfaceFactory;

class TranslationProcessor
{
    private StoreManagerInterface $storeManager;
    private NodeTranslationRepositoryInterface $nodeTranslationRepository;
    private NodeTranslationInterfaceFactory $nodeTranslationFactory;
    private LoggerInterface $logger;
    private array $storeCodeToId = [];

    public function __construct(
        StoreManagerInterface $storeManager,
        NodeTranslationRepositoryInterface $nodeTranslationRepository,
        NodeTranslationInterfaceFactory $nodeTranslationFactory,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->nodeTranslationRepository = $nodeTranslationRepository;
        $this->nodeTranslationFactory = $nodeTranslationFactory;
        $this->logger = $logger;
        $this->initializeStoreMap();
    }

    private function initializeStoreMap(): void
    {
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $this->storeCodeToId[$store->getCode()] = (int)$store->getId();
        }
    }

    public function processTranslations(int $nodeId, array $translations): void
    {
        if (empty($translations)) {
            return;
        }

        foreach ($translations as $storeCode => $title) {
            if (!isset($this->storeCodeToId[$storeCode])) {
                continue; // Skip if store code doesn't exist
            }

            $storeId = $this->storeCodeToId[$storeCode];
            $translation = $this->nodeTranslationFactory->create();
            $translation->setNodeId($nodeId)
                ->setStoreId($storeId)
                ->setTitle($title);

            try {
                $this->nodeTranslationRepository->save($translation);
            } catch (\Exception $e) {
                $this->logger->error(
                    sprintf('Failed to save translation for node %d, store %s: %s', $nodeId, $storeCode, $e->getMessage())
                );
            }
        }
    }
}
