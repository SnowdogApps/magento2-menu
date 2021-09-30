<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\GraphQl\Resolver\DataProvider\Menu as MenuDataProvider;

class Node
{
    /**
     * GraphQL type fields.
     */
    const TEMPLATE_FIELD = 'node_template';
    const SUBMENU_TEMPLATE_FIELD = 'submenu_template';

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var MenuDataProvider
     */
    private $menuDataProvider;

    /**
     * @var array
     */
    private $loadedNodes = [];

    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        MenuDataProvider $menuDataProvider
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->menuDataProvider = $menuDataProvider;
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

        foreach ($nodes as $node) {
            if ($node->getIsActive()) {
                $this->loadedNodes[$identifier][(int) $node->getId()] = $this->convertData($node);
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
            self::TEMPLATE_FIELD => $node->getNodeTemplate(),
            self::SUBMENU_TEMPLATE_FIELD => $node->getSubmenuTemplate(),
            NodeInterface::CREATION_TIME => $node->getCreationTime(),
            NodeInterface::UPDATE_TIME => $node->getUpdateTime(),
            NodeInterface::ADDITIONAL_DATA => $node->getAdditionalData()
        ];
    }
}
