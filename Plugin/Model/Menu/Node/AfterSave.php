<?php
declare(strict_types=1);

namespace Snowdog\Menu\Plugin\Model\Menu\Node;

use Snowdog\Menu\Api\NodeTranslationRepositoryInterface;
use Snowdog\Menu\Api\Data\NodeTranslationInterface;
use Snowdog\Menu\Api\Data\NodeTranslationInterfaceFactory;
use Snowdog\Menu\Model\Menu\Node;

class AfterSave
{
    /**
     * @param NodeTranslationRepositoryInterface $nodeTranslationRepository
     * @param NodeTranslationInterfaceFactory $nodeTranslationFactory
     */
    public function __construct(
        private readonly NodeTranslationRepositoryInterface $nodeTranslationRepository,
        private readonly NodeTranslationInterfaceFactory $nodeTranslationFactory
    ) {}

    /**
     * Save translations after node is saved
     *
     * @param Node $subject
     * @param Node $result
     * @return Node
     */
    public function afterSave(Node $subject, Node $result): Node
    {
        $translations = $result->getData('translations');

        if (!is_array($translations)) {
            return $result;
        }

        $nodeId = (int)$result->getId();
        $existingTranslations = $this->nodeTranslationRepository->getByNodeId($nodeId);
        $existingTranslationMap = [];

        // Create a map of existing translations by store ID for easy lookup
        foreach ($existingTranslations as $translation) {
            $storeId = $translation->getStoreId();
            $existingTranslationMap[$storeId] = $translation;
        }

        // Process new/updated translations
        foreach ($translations as $translation) {
            if (!isset($translation['store_id']) || !isset($translation['value'])) {
                continue;
            }

            $storeId = (int)$translation['store_id'];
            $newValue = $translation['value'];

            // If translation exists for this store
            if (isset($existingTranslationMap[$storeId])) {
                $existingTranslation = $existingTranslationMap[$storeId];
                // Only update if value has changed
                if ($existingTranslation->getTitle() !== $newValue) {
                    $existingTranslation->setTitle($newValue);
                    $this->nodeTranslationRepository->save($existingTranslation);
                }
                // Remove from map as it's been processed
                unset($existingTranslationMap[$storeId]);
            } else {
                // Create new translation
                $nodeTranslation = $this->nodeTranslationFactory->create();
                $nodeTranslation->setNodeId($nodeId);
                $nodeTranslation->setStoreId($storeId);
                $nodeTranslation->setTitle($newValue);
                $this->nodeTranslationRepository->save($nodeTranslation);
            }
        }

        // Delete translations that no longer exist
        foreach ($existingTranslationMap as $translation) {
            $this->nodeTranslationRepository->delete($translation);
        }

        return $result;
    }
}
