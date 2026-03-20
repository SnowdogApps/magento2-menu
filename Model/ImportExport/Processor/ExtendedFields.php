<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor;

/**
 * Import/Export file menu entity extended fields. (i.e. Fields that are not part of menu DB table.)
 */
class ExtendedFields
{
    const NODES = 'nodes';
    const STORES = 'stores';
    const TRANSLATIONS = 'translations';

    const FIELDS = [
        self::NODES,
        self::STORES,
        self::TRANSLATIONS
    ];
}
