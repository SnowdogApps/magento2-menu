<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Validator;

use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class ValidationAggregateError extends \Exception
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
    public function addError($error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function flush(): array
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }
}
