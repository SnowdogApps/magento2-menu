<?php

namespace Snowdog\Menu\Model;

use Magento\ImportExport\Model\Import as ParentClass;

class Import extends ParentClass
{
    const ENTITY = 'snowdog_menu';

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return self::ENTITY;
    }

    public function deleteImportFile()
    {
        $sourceFile = $this->getWorkingDir() . DIRECTORY_SEPARATOR . self::ENTITY . '.csv';
        $this->_varDirectory->delete($this->_varDirectory->getRelativePath($sourceFile));
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function createHistoryReport($sourceFileRelative, $entity, $extension = null, $result = null)
    {
        return $this; // Disable import history report.
    }
}
