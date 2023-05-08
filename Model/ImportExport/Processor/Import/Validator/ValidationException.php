<?php declare(strict_types=1);

namespace Snowdog\Menu\Model\ImportExport\Processor\Import\Validator;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class ValidationException extends LocalizedException
{
    /**
     * @var array
     */
    private $invalidNodePath;

    public function __construct(Phrase $phrase, Exception $cause = null, $code = 0, $invalidNode = [])
    {
        parent::__construct($phrase, $cause, $code);
        $this->invalidNodePath = $invalidNode;
    }

    public function getInvalidNodePath(): array
    {
        return $this->invalidNodePath;
    }
}
