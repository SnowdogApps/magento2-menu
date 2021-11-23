<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\Menu\Node\Image;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class File
{
    const UPLOAD_FILE_ID = 'image';
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png'];
    const PATH = 'snowdog/menu/node';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ImageAdapterFactory
     */
    private $imageAdapterFactory;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Filesystem $filesystem,
        ImageAdapterFactory $imageAdapterFactory,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->imageAdapterFactory = $imageAdapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function upload(): array
    {
        $uploader = $this->uploaderFactory->create(['fileId' => self::UPLOAD_FILE_ID]);
        $imageAdapter = $this->imageAdapterFactory->create();

        $uploader->setAllowedExtensions(self::ALLOWED_EXTENSIONS);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $uploader->addValidateCallback('menu_node_image', $imageAdapter, 'validateUploadFile');

        $result = $uploader->save($this->getAbsolutePath());

        return ['file' => $result['file'], 'url' => $this->getUrl($result['file'])];
    }

    public function getUrl(string $file): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::PATH . $file;
    }

    public function getUploadFileId(): string
    {
        return self::UPLOAD_FILE_ID;
    }

    public function delete(string $file): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory->delete(self::PATH . $file);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws FileSystemException
     */
    public function clone(string $file): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $fileFullPath = $mediaDirectory->getAbsolutePath(self::PATH . $file);
        $fileCloneName = Uploader::getNewFileName($fileFullPath);
        $fileClonePath = Uploader::getDispersionPath($fileCloneName) . '/' . $fileCloneName;
        $fileClone = $mediaDirectory->getAbsolutePath(self::PATH . $fileClonePath);

        try {
            $mediaDirectory->copyFile($fileFullPath, $fileClone);
        } catch (Exception $exception) {
            $this->logger->critical($exception, ['origin' => 'snowdog-menu-node-image-cloner']);
            throw new FileSystemException(__('Could not clone node image file "%1".', $file));
        }

        return $fileClonePath;
    }

    private function getAbsolutePath(): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $mediaDirectory->getAbsolutePath(self::PATH);
    }
}
