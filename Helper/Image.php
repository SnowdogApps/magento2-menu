<?php

declare(strict_types=1);

namespace Snowdog\Menu\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Image extends AbstractHelper
{
    /**
     * Custom directory relative to the "media" folder
     */
    const DIRECTORY = 'snowdog_menu/images';

    const CATALOG_PRODUCT_DIR = 'catalog/product';

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Resize image
     *
     * @param string $image
     * @param string|null $width
     * @param string|null $height
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function resize(string $image, $width = null, $height = null): ?string
    {
        $path = $this->getPath($width, $height);
        $absoluteImagePath = $this->mediaDirectory->getAbsolutePath(self::CATALOG_PRODUCT_DIR) . $image;
        $resizedImagePath = $this->mediaDirectory->getAbsolutePath($path) . $image;

        if (!$this->fileExists($absoluteImagePath)) {
            return null;
        }

        if (!$this->fileExists($path . $image)) {
            $imageFactory = $this->imageFactory->create();
            $imageFactory->open($absoluteImagePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->keepFrame(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize($width, $height);
            $imageFactory->save($resizedImagePath);
        }

        return $this->getMediaUrl() . $path . $image;
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    private function fileExists(string $filename): bool
    {
        return $this->mediaDirectory->isFile($filename);
    }

    /**
     * @param string|null $width
     * @param string|null $height
     * @return string
     */
    private function getPath($width = null, $height = null): string
    {
        $path = self::DIRECTORY;
        if ($width !== null) {
            $path .= DIRECTORY_SEPARATOR . $width . 'x';
            if ($height !== null) {
                $path .= $height ;
            }
        }

        return $path;
    }

    /**
     * @return string
     */
    private function getMediaUrl(): string
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        } catch (NoSuchEntityException $exception) {
            return '';
        }
    }
}
