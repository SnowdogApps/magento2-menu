<?php

declare(strict_types=1);

namespace Snowdog\Menu\Service\Menu;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Api\Data\MenuInterfaceFactory;
use Snowdog\Menu\Api\Data\NodeInterfaceFactory;
use Snowdog\Menu\Api\MenuRepositoryInterface;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Menu\Identifier as MenuIdentifierProcessor;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Service\Menu\Nodes as MenuNodes;

class Cloner
{
    /**
     * @var ResourceConnection
     */
    private $resource;

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
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var MenuNodes
     */
    private $menuNodes;

    public function __construct(
        ResourceConnection $resource,
        MenuInterfaceFactory $menuFactory,
        NodeInterfaceFactory $nodeFactory,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        MenuIdentifierProcessor $menuIdentifierProcessor,
        NodeTypeProvider $nodeTypeProvider,
        MenuNodes $menuNodes
    ) {
        $this->resource = $resource;
        $this->menuFactory = $menuFactory;
        $this->nodeFactory = $nodeFactory;
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->menuIdentifierProcessor = $menuIdentifierProcessor;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->menuNodes = $menuNodes;
    }

    /**
     * @throws Exception
     */
    public function clone(MenuInterface $menu): MenuInterface
    {
        $menuClone = $this->menuFactory->create();

        $menuClone->setData($menu->getData());
        $menuClone->setId(null);
        $menuClone->setIsActive(false);
        $menuClone->setIdentifier(
            $this->menuIdentifierProcessor->getNewIdentifier($menu->getIdentifier())
        );

        $connection = $this->resource->getConnection();
        $connection->beginTransaction();

        try {
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

                $this->nodeTypeProvider
                    ->getTypeModel($node->getType())
                    ->processNodeClone($node, $nodeClone);

                $this->nodeRepository->save($nodeClone);

                $nodeIdMap[$node->getId()] = $nodeClone->getId();
            }

            $connection->commit();
        } catch (Exception $exception) {
            $connection->rollBack();
            throw $exception;
        }

        return $menuClone;
    }
}
