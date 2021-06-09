<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\GraphQl\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Snowdog\Menu\Api\Data\NodeInterface;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;

class Node
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository
    ) {
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getNodesByMenuIdentifier(string $identifier, int $store): array
    {
        $menu = $this->menuRepository->get($identifier, $store);

        if (!$menu->getId()) {
            throw new NoSuchEntityException(
                __('Could not find a menu with identifier "%1".', $identifier)
            );
        }

        $nodes = $this->nodeRepository->getByIdentifier($identifier);
        $data = [];

        foreach ($nodes as $node) {
            if ($node->getIsActive()) {
                $data[(int) $node->getId()] = $this->convertData($node);
            }
        }

        return $data;
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
            NodeInterface::TARGET => (int) $node->getTarget(),
            NodeInterface::IMAGE => $node->getImage(),
            NodeInterface::IMAGE_ALT_TEXT => $node->getImageAltText(),
            NodeInterface::CREATION_TIME => $node->getCreationTime(),
            NodeInterface::UPDATE_TIME => $node->getUpdateTime(),
            NodeInterface::ADDITIONAL_DATA => $node->getAdditionalData()
        ];
    }
}
