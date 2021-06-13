<?php

declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Menu\Identifier as MenuIdentifierProcessor;
use Snowdog\Menu\Model\Menu\Node\Image\File as NodeImage;
use Snowdog\Menu\Service\Menu\Nodes as MenuNodes;

class Cloner
{
    /**
     * @var MenuInterfaceFactory
     */
    private $menuFactory;

    /**
     * @var NodeInterfaceFactory
     */
    private $nodeFactory;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var MenuIdentifierProcessor
     */
    private $menuIdentifierProcessor;

    /**
     * @var NodeImage
     */
    private $nodeImage;

    /**
     * @var MenuNodes
     */
    private $menuNodes;

    public function __construct(
        MenuInterfaceFactory $menuFactory,
        NodeInterfaceFactory $nodeFactory,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        MenuIdentifierProcessor $menuIdentifierProcessor,
        NodeImage $nodeImage,
        MenuNodes $menuNodes
    ) {
        $this->menuFactory = $menuFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->menuIdentifierProcessor = $menuIdentifierProcessor;
        $this->nodeImage = $nodeImage;
        $this->menuNodes = $menuNodes;
    }

    public function clone(MenuInterface $menu): MenuInterface
    {
        $menuClone = $this->menuFactory->create();

        $menuClone->setData($menu->getData());
        $menuClone->setId(null);
        $menuClone->setIdentifier(
            $this->menuIdentifierProcessor->getNewIdentifier($menu->getIdentifier())
        );

        $this->menuRepository->save($menuClone);
        $menuClone->saveStores($menu->getStores());

        $menuCloneId = $menuClone->getId();
        $nodeIdMap = [];

        foreach ($this->menuNodes->getList($menu) as $node) {
            $nodeClone = $this->nodeFactory->create();

            $nodeClone->setData($node->getData());
            $nodeClone->setId(null);
            $nodeClone->setMenuId($menuCloneId);

            if (isset($nodeIdMap[$node->getParentId()])) {
                $nodeClone->setParentId($nodeIdMap[$node->getParentId()]);
            }

            if ($node->getImage()) {
                $nodeClone->setImage($this->nodeImage->clone($node->getImage()));
            }

            $this->nodeRepository->save($nodeClone);

            $nodeIdMap[$node->getId()] = $nodeClone->getId();
        }

        return $menuClone;
    }
}
