<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\FieldProcessor;

class Boolean
{
    const TRUE_VALUES = [1, 'true'];

    /**
     * @param mixed $data
     */
    public function getValue($data): bool
    {
        $data = strtolower((string) $data);
        return in_array($data, self::TRUE_VALUES);
    }
}
