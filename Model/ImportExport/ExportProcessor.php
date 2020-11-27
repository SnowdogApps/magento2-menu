<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\App\ResponseInterface;

class ExportProcessor
{
    /**
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var Processor\Export\Menu
     */
    private $menu;

    /**
     * @var Processor\Export\Node
     */
    private $node;

    public function __construct(
        ExportFile $exportFile,
        Processor\Export\Menu $menu,
        Processor\Export\Node $node
    ) {
        $this->exportFile = $exportFile;
        $this->menu = $menu;
        $this->node = $node;
    }

    public function getDownloadFile(int $menuId): ResponseInterface
    {
        return $this->exportFile->generateDownloadFile($menuId, $this->getExportData($menuId));
    }

    private function getExportData(int $menuId): array
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->node->getList($menuId);

        if ($nodes) {
            $data[Processor\ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
