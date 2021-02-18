<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Import\SampleData\Processor\Field;

class Description
{
    const OPTIONAL_FIELD_LABEL = '[Optional]';
    const TIMESTAMP_DEFAULT_VALUE = 'current_timestamp()';

    public function getDescription(array $fieldDescription): string
    {
        $details = [];

        if ($fieldDescription['NULLABLE']) {
            $details[] = self::OPTIONAL_FIELD_LABEL;
        }

        if (isset($fieldDescription['DEFAULT'])
            && $fieldDescription['DEFAULT'] !== ''
            && $fieldDescription['DEFAULT'] !== self::TIMESTAMP_DEFAULT_VALUE
        ) {
            $details[] = '[Default: ' . $fieldDescription['DEFAULT'] . ']';
        }

        return $details ? implode(' - ', $details) : '';
    }
}
