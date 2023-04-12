<?php declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import;

use Magento\Framework\Message\Manager;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Node\Validator\TreeTrace;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationAggregateError;
use Snowdog\Menu\Model\ImportExport\Processor\Import\Validator\ValidationException;

class InvalidNodes
{
    /**
     * @var TreeTrace
     */
    private $treeTrace;

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(TreeTrace $treeTrace, Manager $manager)
    {
        $this->treeTrace = $treeTrace;
        $this->manager = $manager;
    }

    public function process(array &$data, ValidationAggregateError $error): array
    {
        return $this->stripInvalidNodes($data, $error);
    }

    /**
     * @param array $data
     * @param ValidationAggregateError $validationAggregateError
     * @return array
     */
    private function stripInvalidNodes(array &$data, ValidationAggregateError $validationAggregateError): array
    {
        foreach ($validationAggregateError->getErrors() as $error) {
            if ($error instanceof ValidationException) {
                if (empty($error->getInvalidNodePath())) {
                    continue;
                }

                $this->unsetItemByPath($error->getInvalidNodePath(), $data);
                $this->manager->addNoticeMessage(__(
                    "Invalid node %1 not imported. %2",
                    implode(' > ', $error->getInvalidNodePath()),
                    $error->getMessage()
                ));
            }
        }
        return $data;
    }

    /**
     * Unsets specific $data element by $path
     */
    private function unsetItemByPath(array $path, array &$data): void
    {
        $path = $this->updatePathValues($path);

        $dataElement =& $data;
        $lastItemKey = array_pop($path);

        foreach ($path as $key) {
            $dataElement =& $dataElement["nodes"][$key];
        }

        $dataElement["nodes"][$lastItemKey] = null;
    }

    private function updatePathValues(array $path): array
    {
        if ($this->treeTrace->isEnabledNodeIdAddend()) {
            foreach ($path as &$idx) {
                $idx--;
            }
        }

        return $path;
    }
}
