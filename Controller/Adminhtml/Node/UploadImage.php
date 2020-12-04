<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\Menu\Node\Image\File as ImageFile;

class UploadImage extends Action implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Snowdog_Menu::menus';

    /**
     * @var JsonResultFactory
     */
    private $jsonResultFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageFile
     */
    private $imageFile;

    public function __construct(
        Context $context,
        JsonResultFactory $jsonResultFactory,
        LoggerInterface $logger,
        ImageFile $imageFile
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->logger = $logger;
        $this->imageFile = $imageFile;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $jsonResult = $this->jsonResultFactory->create();
        $currentImage = $this->getRequest()->getPost('current_image');

        try {
            if ($currentImage) {
                $this->imageFile->delete($currentImage);
            }

            $result = $this->imageFile->upload();
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $result = ['error' => __('Menu node image upload failed.')];
        }

        $jsonResult->setData($result);

        return $jsonResult;
    }
}
