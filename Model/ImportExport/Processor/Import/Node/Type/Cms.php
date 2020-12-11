<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Type;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Cms
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var array
     */
    private $cachedBlocks = [];

    /**
     * @var array
     */
    private $cachedPages = [];

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        PageRepositoryInterface $pageRepository
    ) {
        $this->blockRepository = $blockRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param string $identifier
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getBlock($identifier): ?BlockInterface
    {
        if (isset($this->cachedBlocks[$identifier])) {
            return $this->cachedBlocks[$identifier];
        }

        try {
            $block = $this->blockRepository->getById($identifier);
        } catch (NoSuchEntityException $exception) {
            $block = null;
        }

        $this->cachedBlocks[$identifier] = $block;

        return $block;
    }

    /**
     * @param string $identifier
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getPage($identifier): ?PageInterface
    {
        if (isset($this->cachedPages[$identifier])) {
            return $this->cachedPages[$identifier];
        }

        try {
            $page = $this->pageRepository->getById($identifier);
        } catch (NoSuchEntityException $exception) {
            $page = null;
        }

        $this->cachedPages[$identifier] = $page;

        return $page;
    }
}
