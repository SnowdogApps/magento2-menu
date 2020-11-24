<?php

namespace Snowdog\Menu\Model\ImportExport;

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
            $data[Processor\ExtendedFields::NODES] = $nodes;
        }

        return $data;
    }
}
