<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor;

use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field\Description as DescriptionProcessor;
use Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field\Value as ValueProcessor;

class Field
{
    /**
     * @var DescriptionProcessor
     */
    private $descriptionProcessor;

    /**
     * @var ValueProcessor
     */
    private $valueProcessor;

    public function __construct(DescriptionProcessor $descriptionProcessor, ValueProcessor $valueProcessor)
    {
        $this->descriptionProcessor = $descriptionProcessor;
        $this->valueProcessor = $valueProcessor;
    }

    public function getData(string $field, array $fieldDescription, array $defaultData = []): string
    {
        $data = [
            'value' => $this->valueProcessor->getValue($field, $fieldDescription, $defaultData),
            'description' => $this->descriptionProcessor->getDescription($fieldDescription)
        ];

        return implode(' - ', array_filter($data));
    }
}
