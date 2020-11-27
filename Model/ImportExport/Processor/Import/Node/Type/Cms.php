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
    const BLOCK_NODE_TYPE = 'cms_block';
    const PAGE_NODE_TYPE = 'cms_page';

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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getBlock(string $identifier): ?BlockInterface
    {
        if (isset($this->cachedBlocks[$identifier])) {
            return $this->cachedBlocks[$identifier];
        }

        try {
            $block = $this->blockRepository->getById($identifier);
            $this->cachedBlocks[$identifier] = $block;
        } catch (NoSuchEntityException $exception) {
            $block = null;
        }

        return $block;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getPage(string $identifier): ?PageInterface
    {
        if (isset($this->cachedPages[$identifier])) {
            return $this->cachedPages[$identifier];
        }

        try {
            $page = $this->pageRepository->getById($identifier);
            $this->cachedPages[$identifier] = $page;
        } catch (NoSuchEntityException $exception) {
            $page = null;
        }

        return $page;
    }
}
