<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

class TreeTrace
{
    /**
     * An addend that converts keys of arrays that their indices start from 0 to one-based numbering.
     */
    const ZERO_BASED_ARRAY_INDEXING_KEY_ADDENED = 1;

    /**
     * @return int
     */
    private $nodeIdAddend = self::ZERO_BASED_ARRAY_INDEXING_KEY_ADDENED;

    /**
     * @param int|string $nodeId
     */
    public function get(array $treeTrace, $nodeId): array
    {
        if ($this->nodeIdAddend) {
            $nodeId = ((int) $nodeId) + $this->nodeIdAddend;
        }

        $treeTrace[] = $nodeId;

        return $treeTrace;
    }

    /**
     * @param int|string|null $nodeId
     */
    public function getBreadcrumbs(array $treeTrace, $nodeId = null): string
    {
        if ($nodeId !== null) {
            $treeTrace = $this->get($treeTrace, $nodeId);
        }

        return implode(' > ', $treeTrace);
    }

    public function disableNodeIdAddend(): void
    {
        $this->nodeIdAddend = 0;
    }
}
