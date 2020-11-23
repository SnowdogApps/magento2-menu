<?php

namespace Snowdog\Menu\Model\ImportExport\Processor;

use Snowdog\Menu\Model\ImportExport\ExportFile;

class Export
{
    /**
     * @var ExportFile
     */
    private $exportFile;

    /**
     * @var Export\Menu
     */
    private $menu;

    /**
     * @var Export\Node
     */
    private $node;

    public function __construct(ExportFile $exportFile, Export\Menu $menu, Export\Node $node)
    {
        $this->exportFile = $exportFile;
        $this->menu = $menu;
        $this->node = $node;
    }

    /**
     * @param int $menuId
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getDownloadFile($menuId)
    {
        return $this->exportFile->generateDownloadFile($menuId, $this->getExportData($menuId));
    }

    /**
     * @param int $menuId
     * @return array
     */
    private function getExportData($menuId)
    {
        $data = $this->menu->getData($menuId);
        $nodes = $this->node->getList($menuId);

        if ($nodes) {
            $data[ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
