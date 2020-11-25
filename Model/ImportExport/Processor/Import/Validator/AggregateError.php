<?php

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Validator;

use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class AggregateError
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param string|\Magento\Framework\Phrase $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function flush()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }
    }

    /**
     * @return bool
     */
    public function isFlushable()
    {
        return (bool) $this->errors;
    }
}
