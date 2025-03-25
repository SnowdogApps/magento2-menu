<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Menu as MenuDataProvider;
use Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Node\TypeModel;

class Node
{
    /**
     * GraphQL type fields.
     */
    const TEMPLATE_FIELD = 'node_template';
    const SUBMENU_TEMPLATE_FIELD = 'submenu_template';
    const URL_KEY = 'url_key';

    const NODE_TYPE_CUSTOM_URL = 'custom_url';

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var MenuDataProvider
     */
    private $menuDataProvider;

    /**
     * @var TypeModel
     */
    private $typeModel;

    /**
     * @var array
     */
    private $loadedNodes = [];

    /**
     * @var array
     */
    private $loadedModels = [];

    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        MenuDataProvider $menuDataProvider,
        TypeModel $typeModel
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->menuDataProvider = $menuDataProvider;
        $this->typeModel = $typeModel;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getNodesByMenuIdentifier(string $identifier, int $storeId): array
    {
        if (isset($this->loadedNodes[$identifier])) {
            return $this->loadedNodes[$identifier];
        }

        $menu = $this->menuDataProvider->get($identifier, $storeId);

        if (!$menu) {
            throw new NoSuchEntityException(
                __('Could not find a menu with identifier "%1".', $identifier)
            );
        }

        $nodes = $this->nodeRepository->getByIdentifier($identifier);
        $this->loadModels($nodes, $storeId);
        foreach ($nodes as $node) {
            if ($node->getIsActive()) {
                $this->loadedNodes[$identifier][(int) $node->getId()] = $this->convertData($node);
                $this->loadedNodes[$identifier][(int) $node->getId()][self::URL_KEY] = $this->getUrlKey($node);
            }
        }

        if (!isset($this->loadedNodes[$identifier])) {
            $this->loadedNodes[$identifier] = [];
        }

        return $this->loadedNodes[$identifier];
    }

    private function convertData(NodeInterface $node): array
    {
        return [
            NodeInterface::NODE_ID => (int) $node->getId(),
            NodeInterface::MENU_ID => (int) $node->getMenuId(),
            NodeInterface::TYPE => $node->getType(),
            NodeInterface::CONTENT => $node->getContent(),
            NodeInterface::CLASSES => $node->getClasses(),
            NodeInterface::PARENT_ID => (int) $node->getParentId(),
            NodeInterface::POSITION => (int) $node->getPosition(),
            NodeInterface::LEVEL => (int) $node->getLevel(),
            NodeInterface::TITLE => $node->getTitle(),
            NodeInterface::TARGET => (bool) $node->getTarget(),
            NodeInterface::IMAGE => $node->getImage(),
            NodeInterface::IMAGE_ALT_TEXT => $node->getImageAltText(),
            NodeInterface::IMAGE_WIDTH => $node->getImageWidth(),
            NodeInterface::IMAGE_HEIGHT => $node->getImageHeight(),
            self::TEMPLATE_FIELD => $node->getNodeTemplate(),
            self::SUBMENU_TEMPLATE_FIELD => $node->getSubmenuTemplate(),
            NodeInterface::CREATION_TIME => $node->getCreationTime(),
            NodeInterface::UPDATE_TIME => $node->getUpdateTime(),
            NodeInterface::ADDITIONAL_DATA => $node->getAdditionalData(),
            NodeInterface::SELECTED_ITEM_ID => $node->getSelectedItemId(),
            NodeInterface::CUSTOMER_GROUPS => $node->getCustomerGroups(),
            NodeInterface::HIDE_IF_EMPTY => $node->getHideIfEmpty(),
        ];
    }

    /**
     * @param $nodes
     * @param $storeId
     * @return void
     */
    private function loadModels($nodes, $storeId): void
    {
        $modelsToLoadByType = [];
        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            $type = $node->getType();
            if (isset($this->loadedModels[$type][$node->getContent()])) {
                continue;
            }
            if (!in_array($type, TypeModel::TYPES)) {
                continue;
            }
            $modelsToLoadByType[$type][] = $node->getContent();
        }

        foreach ($modelsToLoadByType as $type => $ids) {
            if (!is_array($ids)) {
                continue;
            }
            try {
                $this->loadedModels[$type] = $this->typeModel->getModels($type, $ids ?? [], $storeId);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param NodeInterface $node
     * @return string|null
     */
    private function getUrlKey(NodeInterface $node): ?string
    {
        if (in_array($node->getType(), TypeModel::TYPES)) {
            if (!isset($this->loadedModels[$node->getType()][$node->getContent()])) {
                return null;
            }
            return $this->loadedModels[$node->getType()][$node->getContent()];
        } elseif ($node->getType() == self::NODE_TYPE_CUSTOM_URL) {
            return $node->getContent();
        } else {
            return null;
        }
    }
}
