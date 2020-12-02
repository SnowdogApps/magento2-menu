<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File;

class Upload
{
    /**
     * @var Upload\Content
     */
    private $uploadContent;

    /**
     * @var Upload\Source
     */
    private $uploadSource;

    public function __construct(Upload\Content $uploadContent, Upload\Source $uploadSource)
    {
        $this->uploadContent = $uploadContent;
        $this->uploadSource = $uploadSource;
    }

    public function uploadFileAndGetData(): array
    {
        return $this->uploadContent->get($this->uploadSource->upload());
    }
}
