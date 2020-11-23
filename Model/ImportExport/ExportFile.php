<?php

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory as HttpFileFactory;
use Magento\Framework\Filesystem;

class ExportFile
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

    /**
     * @param string $fileId
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function generateDownloadFile($fileId, array $data)
    {
        $file = $this->getFile($fileId);

        $this->varDirectory->create(self::EXPORT_DIR);
        $stream = $this->varDirectory->openFile($file, 'w+');
        $stream->lock();

        $stream->write($this->yaml->dump($data));

        $stream->unlock();
        $stream->close();

        return $this->httpFileFactory->create(
            self::DOWNLOAD_FILE_NAME . '-' . $fileId . '.' . self::FILE_EXTENSION,
            ['type' => 'filename', 'value' => $file, 'rm' => true],
            DirectoryList::VAR_DIR
        );
    }

    /**
     * @param string $fileId
     * @return string
     */
    private function getFile($fileId)
    {
        return self::EXPORT_DIR . DIRECTORY_SEPARATOR
            . ImportSource::ENTITY . '-' . $fileId . '-' . hash('sha256', microtime())
            . '.' . self::FILE_EXTENSION;
    }
}
