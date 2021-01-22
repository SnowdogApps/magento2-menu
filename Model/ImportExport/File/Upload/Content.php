<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File\Upload;

use LogicException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Snowdog\Menu\Model\ImportExport\File\Yaml;

class Content
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $varDirectory;

    /**
     * @var Yaml
     */
    private $yaml;

    public function __construct(Filesystem $filesystem, Yaml $yaml)
    {
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->yaml = $yaml;
    }

    /**
     * @throws LogicException
     */
    public function flush(string $sourceFile): array
    {
        $sourceFilePath = $this->varDirectory->getRelativePath($sourceFile);

        try {
            $stream = $this->varDirectory->openFile($sourceFilePath, 'r');
        } catch (FileSystemException $exception) {
            throw new LogicException(__('Unable to open uploaded file.'));
        }

        $data = '';
        while (!$stream->eof()) {
            $data .= $stream->read(1024);
        }

        $stream->close();
        $this->varDirectory->delete($sourceFilePath);

        return $this->yaml->parse($data);
    }
}
