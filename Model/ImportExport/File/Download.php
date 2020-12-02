<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory as HttpFileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filesystem;
use Snowdog\Menu\Model\ImportExport\File\Upload\Source as UploadSource;
use Snowdog\Menu\Model\ImportExport\File\Yaml;

class Download
{
    const EXPORT_DIR = 'importexport';
    const FILE_EXTENSION = 'yaml';
    const DOWNLOAD_FILE_NAME = 'menu';

    /**
     * @var HttpFileFactory
     */
    private $httpFileFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $varDirectory;

    /**
     * @var Yaml
     */
    private $yaml;

    public function __construct(HttpFileFactory $httpFileFactory, Filesystem $filesystem, Yaml $yaml)
    {
        $this->httpFileFactory = $httpFileFactory;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->yaml = $yaml;
    }

    public function generateDownloadFile(string $fileId, array $data): ResponseInterface
    {
        $filePath = $this->getFilePath($fileId);

        $this->varDirectory->create(self::EXPORT_DIR);
        $stream = $this->varDirectory->openFile($filePath, 'w+');
        $stream->lock();

        $stream->write($this->yaml->dump($data));

        $stream->unlock();
        $stream->close();

        return $this->httpFileFactory->create(
            self::DOWNLOAD_FILE_NAME . '-' . $fileId . '.' . self::FILE_EXTENSION,
            ['type' => 'filename', 'value' => $filePath, 'rm' => true],
            DirectoryList::VAR_DIR
        );
    }

    private function getFilePath(string $fileId): string
    {
        return self::EXPORT_DIR . DIRECTORY_SEPARATOR
            . UploadSource::ENTITY . '-' . $fileId . '-' . hash('sha256', microtime())
            . '.' . self::FILE_EXTENSION;
    }
}
