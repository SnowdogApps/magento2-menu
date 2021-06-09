<?php

declare(strict_types=1);

namespace Snowdog\Menu\Controller\Adminhtml\Node;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Psr\Log\LoggerInterface;
use Snowdog\Menu\Model\Menu\Node\Image\File as ImageFile;
use Snowdog\Menu\Model\Menu\Node\Image\Node as ImageNode;

class DeleteImage extends Action implements HttpPostActionInterface
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

        $image = $request->getPost('image');
        $nodeId = $request->getPost('node_id');

        try {
            $this->imageFile->delete($image);

            if ($nodeId) {
                $this->imageNode->updateNodeImage((int) $nodeId, null);
            }

            $result = ['status' => 1];
        } catch (FileSystemException $exception) {
            $this->logger->critical($exception);
            $result = ['status' => 0];
        }

        $jsonResult->setData($result);

        return $jsonResult;
    }
}
