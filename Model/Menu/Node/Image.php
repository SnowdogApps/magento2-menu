<?php

namespace Snowdog\Menu\Model\Menu\Node;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

class Image
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

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        ImageAdapterFactory $imageAdapterFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->imageAdapterFactory = $imageAdapterFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function upload()
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

    /**
     * @param string $file
     * @return string
     */
    public function getUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::PATH . $file;
    }

    /**
     * @return string
     */
    public function getUploadFileId()
    {
        return self::UPLOAD_FILE_ID;
    }

    /**
     * @param string $file
     */
    public function delete($file)
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory->delete(self::PATH . $file);
    }

    /**
     * @return string
     */
    private function getAbsolutePath()
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        return $mediaDirectory->getAbsolutePath(self::PATH);
    }
}
