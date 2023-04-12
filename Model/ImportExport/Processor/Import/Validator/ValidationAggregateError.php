<?php

declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Validator;

use Exception;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Phrase;

class ValidationAggregateError extends Exception
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
     * @param string|Phrase|Exception $error
     */
    public function addError($error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function flush(): void
    {
        foreach ($this->getErrorMessages() as $errorMessage) {
            $this->messageManager->addErrorMessage($errorMessage);
        }

        $this->errors = [];
    }

    public function getErrorMessages(): array
    {
        $errorMessages = [];
        foreach ($this->errors as $error) {
            if (is_string($error) || $error instanceof Phrase) {
                $errorMessages[] = $error;
            }

            if ($error instanceof Exception) {
                $errorMessages[] = $error->getMessage();
            }
        }

        return $errorMessages;
    }
}
