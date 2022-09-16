<?php

namespace Snowdog\Menu\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\Menu\Edit;
use Snowdog\Menu\Model\Menu\Node\Image\File as ImageFile;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Model\VueProvider;

class Nodes extends Template implements TabInterface
{
    const IMAGE_UPLOAD_URL = 'snowmenu/node/uploadimage';
    const IMAGE_DELETE_URL = 'snowmenu/node/deleteimage';

    protected $_template = 'menu/nodes.phtml';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * @var ImageFile
     */
    private $imageFile;
    /**
     * @var NodeTypeProvider
     */
    private $nodeTypeProvider;

    /**
     * @var VueProvider
     */
    private $vueProvider;

    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        ImageFile $imageFile,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        VueProvider $vueProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->imageFile = $imageFile;
        $this->vueProvider = $vueProvider;
    }

    public function renderNodes()
    {
        $menu = $this->registry->registry(Edit::REGISTRY_CODE);
        $data = [];
        if ($menu) {
            $nodes = $this->nodeRepository->getByMenu($menu->getId());
            if (!empty($nodes)) {
                foreach ($nodes as $node) {
                    $level = $node->getLevel();
                    $parent = $node->getParentId() ?: 0;
                    if (!isset($data[$level])) {
                        $data[$level] = [];
                    }
                    if (!isset($data[$level][$parent])) {
                        $data[$level][$parent] = [];
                    }
                    $data[$level][$parent][] = $node;
                }
                return $this->renderNodeList(0, null, $data);
            }
        }
        return $data;
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __("Nodes");
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __("Nodes");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getImageUploadUrl()
    {
        return $this->getUrl(self::IMAGE_UPLOAD_URL);
    }

    /**
     * @return string
     */
    public function getImageDeleteUrl()
    {
        return $this->getUrl(self::IMAGE_DELETE_URL);
    }

    /**
     * @return string
     */
    public function getImageUploadFileId()
    {
        return $this->imageFile->getUploadFileId();
    }

    private function renderNodeList($level, $parent, $data)
    {
        if ($parent === null) {
            $parent = 0;
        }
        if (empty($data[$level])) {
            return;
        }
        if (empty($data[$level][$parent])) {
            return;
        }
        $nodes = $data[$level][$parent];
        foreach ($nodes as $node) {
            $menu[] = [
                'is_active' => $node->getIsActive(),
                'is_stored' => true,
                'type' => $node->getType(),
                'content' => $node->getContent(),
                'classes' => $node->getClasses(),
                'target' => $node->getTarget(),
                'node_template' => $node->getNodeTemplate(),
                'submenu_template' => $node->getSubmenuTemplate(),
                'id' => $node->getId(),
                'title' => $node->getTitle(),
                'image' => $node->getImage(),
                'image_url' => $node->getImage() ? $this->imageFile->getUrl($node->getImage()) : null,
                'image_alt_text' => $node->getImageAltText(),
                'columns' => $this->renderNodeList($level + 1, $node->getId(), $data) ?: [],
                'selected_item_id' => $node->getSelectedItemId()
            ];
        }
        return $menu;
    }

    public function getNodeForms()
    {
        return $this->nodeTypeProvider->getEditForms();
    }

    public function getNodeLabels()
    {
        return $this->nodeTypeProvider->getLabels();
    }

    /**
     * @return array
     */
    public function getVueComponents(): array
    {
        return $this->vueProvider->getComponents();
    }
}
