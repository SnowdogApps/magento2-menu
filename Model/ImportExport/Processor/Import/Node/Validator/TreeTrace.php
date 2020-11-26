<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator;

class TreeTrace
{
    /**
     * @param int $nodeNumber
     * @return array
     */
    public function get(array $treeTrace, $nodeNumber)
    {
        $treeTrace[] = $nodeNumber + 1;
        return $treeTrace;
    }

    /**
     * @param int|null $nodeNumber
     * @return string
     */
    public function getBreadcrumbs(array $treeTrace, $nodeNumber = null)
    {
        if ($nodeNumber !== null) {
            $treeTrace = $this->get($treeTrace, $nodeNumber);
        }

        return implode(' > ', $treeTrace);
    }
}
