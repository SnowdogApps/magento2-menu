<?php

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Snowdog\Menu\Model\Menu\Node\Image;

class UploadImage extends Action
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
     * @var Image
     */
    private $image;

    public function __construct(
        Action\Context $context,
        JsonResultFactory $jsonResultFactory,
        Image $image
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->image = $image;

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
                $this->image->delete($currentImage);
            }

            $result = $this->image->upload();
        } catch (\Exception $exception) {
            $result = ['error' => $exception->getMessage(), 'errorcode' => $exception->getCode()];
        }

        $jsonResult->setData($result);
        return $jsonResult;
    }
}
