<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\File;

class Upload
{
    /**
     * @var Upload\Content
     */
    private $content;

    /**
     * @var Upload\Source
     */
    private $source;

    public function __construct(Upload\Content $content, Upload\Source $source)
    {
        $this->content = $content;
        $this->source = $source;
    }

    public function uploadFileAndGetData(): array
    {
        return $this->content->get($this->source->upload());
    }
}
