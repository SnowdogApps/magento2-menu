<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Webapi\Exception as WebapiException;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\Menu\Node\Image\File as ImageFile;
use Snowdog\Menu\Model\Menu\Node\Image\Node as ImageNode;

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

    /**
     * @var ImageNode
     */
    private $imageNode;

    public function __construct(
        Context $context,
        JsonResultFactory $jsonResultFactory,
        LoggerInterface $logger,
        ImageFile $imageFile,
        ImageNode $imageNode
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->logger = $logger;
        $this->imageFile = $imageFile;
        $this->imageNode = $imageNode;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $jsonResult = $this->jsonResultFactory->create();
        $request = $this->getRequest();

        $currentImage = $request->getPost('current_image');
        $nodeId = $request->getPost('node_id');

        try {
            if ($currentImage) {
                $this->imageFile->delete($currentImage);
            }

            $result = $this->imageFile->upload();

            if ($nodeId) {
                $this->imageNode->updateNodeImage((int) $nodeId, $result['file']);
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $jsonResult->setHttpResponseCode(WebapiException::HTTP_INTERNAL_ERROR);

            $result = ['error' => __('Menu node image upload failed.')];
        }

        $jsonResult->setData($result);

        return $jsonResult;
    }
}
