<?php

namespace Snowdog\Menu\Model;

use Magento\ImportExport\Model\Import as ParentModel;

class Import extends ParentModel
{
    const ENTITY = 'snowdog_menu';

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return self::ENTITY;
    }

    /**
     * Move uploaded file and provide source instance.
     *
     * [Backward Compatibility Note]:
     * This method is redefined in order to support some old Magento releases that do not have it.
     * (Releases that are older than 2.1.15, and releases from 2.0.0 to 2.2.6.)
     *
     * TODO: Once those Magento releases are no longer supported, then this method could be removed.
     *
     * @return \Magento\ImportExport\Model\Import\AbstractSource
     */
    public function uploadFileAndGetSource()
    {
        if (method_exists($this, 'uploadFileAndGetSource')) {
            return parent::uploadFileAndGetSource();
        }

        $sourceFile = $this->uploadSource();
        return $this->_getSourceAdapter($sourceFile);
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
