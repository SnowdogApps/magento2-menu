<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

class TreeTrace
{
    /**
     * @return int
     */
    private $nodeIdAddend = 1;

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
