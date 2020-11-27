<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

class TreeTrace
{
    /**
     * @return int
     */
    private $nodeIdAddend = 1;

    /**
     * @param int|string $nodeId
     * @return array
     */
    public function get(array $treeTrace, $nodeId)
    {
        if ($this->nodeIdAddend) {
            $nodeId = (int) $nodeId + $this->nodeIdAddend;
        }

        $treeTrace[] = $nodeId;

        return $treeTrace;
    }

    /**
     * @param int|null $nodeId
     * @return string
     */
    public function getBreadcrumbs(array $treeTrace, $nodeId = null)
    {
        if ($nodeId !== null) {
            $treeTrace = $this->get($treeTrace, $nodeId);
        }

        return implode(' > ', $treeTrace);
    }

    public function disableNodeIdAddend()
    {
        $this->nodeIdAddend = 0;
    }
}
