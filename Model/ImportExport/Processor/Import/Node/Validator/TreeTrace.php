<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

class TreeTrace
{
    /**
     * @param int $nodeId
     * @return array
     */
    public function get(array $treeTrace, $nodeId)
    {
        $treeTrace[] = $nodeId + 1;
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
}
