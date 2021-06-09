<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File\Upload;

use Exception;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Framework\Validation\ValidationException;
use Magento\ImportExport\Helper\Data as ImportExportHelper;
use Magento\ImportExport\Model\Import as ImportModel;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Snowdog\Menu\Model\ImportExport\File\Yaml;
use Zend_Validate_File_Upload;

class Source
{
    const ENTITY = 'snowdog_menu';

    /**
     * @var FileTransferFactory
     */
    private $fileTransferFactory;

    /**
     * @var ImportExportHelper
     */
    private $importExportHelper;

    /**
     * @var ImportModel
     */
    private $import;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    public function __construct(
        FileTransferFactory $fileTransferFactory,
        ImportExportHelper $importExportHelper,
        ImportModel $import,
        UploaderFactory $uploaderFactory
    ) {
        $this->fileTransferFactory = $fileTransferFactory;
        $this->importExportHelper = $importExportHelper;
        $this->import = $import;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @throws ValidatorException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function upload(): string
    {
        $this->validateFile();

        $uploader = $this->uploaderFactory->create(['fileId' => ImportModel::FIELD_NAME_SOURCE_FILE]);
        $uploader->setAllowedExtensions(Yaml::FILE_EXTENSIONS);
        $uploader->skipDbProcessing(true);

        $workingDir = $this->import->getWorkingDir();
        $fileName = self::ENTITY . '-' . hash('sha256', microtime()) . '.' . $uploader->getFileExtension();

        try {
            $result = $uploader->save($workingDir, $fileName);
        } catch (ValidationException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        } catch (Exception $exception) {
            throw new ValidatorException(__('The file cannot be uploaded.'));
        }

        return $result['path'] . $result['file'];
    }

    /**
     * @throws ValidatorException
     */
    private function validateFile(): void
    {
        $fileTransferAdapter = $this->fileTransferFactory->create();

        if (!$fileTransferAdapter->isValid(ImportModel::FIELD_NAME_SOURCE_FILE)) {
            $errors = $fileTransferAdapter->getErrors();

            $errorMessage = $errors[0] === Zend_Validate_File_Upload::INI_SIZE
                ? $this->importExportHelper->getMaxUploadSizeMessage()
                : __('The file was not uploaded.');

            throw new ValidatorException($errorMessage);
        }
    }
}
