<?php

namespace Snowdog\Menu\Block\Adminhtml\Edit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Snowdog\Menu\Api\NodeRepositoryInterface;
use Snowdog\Menu\Controller\Adminhtml\Menu\Edit;
use Snowdog\Menu\Model\CustomerGroupsProvider;
use Snowdog\Menu\Model\Menu\Node\Image\File as ImageFile;
use Snowdog\Menu\Model\NodeTypeProvider;
use Snowdog\Menu\Model\VueProvider;
use Magento\Store\Model\System\Store;
use Snowdog\Menu\Api\NodeTranslationRepositoryInterface;

/**
 * @api
 */
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

    /**
     * @var CustomerGroupsProvider
     */
    private $customerGroupsProvider;

    /**
     * @var Store
     */
    private $systemStore;

    /**
     * @var NodeTranslationRepositoryInterface
     */
    private $nodeTranslationRepository;

    public function __construct(
        Template\Context $context,
        NodeRepositoryInterface $nodeRepository,
        ImageFile $imageFile,
        NodeTypeProvider $nodeTypeProvider,
        Registry $registry,
        VueProvider $vueProvider,
        CustomerGroupsProvider $customerGroupsProvider,
        Store $systemStore,
        NodeTranslationRepositoryInterface $nodeTranslationRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->nodeRepository = $nodeRepository;
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->imageFile = $imageFile;
        $this->vueProvider = $vueProvider;
        $this->customerGroupsProvider = $customerGroupsProvider;
        $this->systemStore = $systemStore;
        $this->nodeTranslationRepository = $nodeTranslationRepository;
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
        $menu = [];

        // Create store view lookup array
        $storeViewLabels = [];
        $websites = $this->systemStore->getWebsiteCollection();
        $websiteNames = [];

        // Create website name lookup array
        foreach ($websites as $website) {
            $websiteNames[$website->getId()] = $website->getName();
        }

        foreach ($this->systemStore->getStoreCollection() as $store) {
            if ($store->isActive()) {
                $websiteName = isset($websiteNames[$store->getWebsiteId()])
                    ? $websiteNames[$store->getWebsiteId()]
                    : '';
                $storeViewLabels[$store->getId()] = [
                    'value' => $store->getId(),
                    'label' => sprintf('%s -> %s', $websiteName, $store->getName())
                ];
            }
        }

        foreach ($nodes as $node) {
            $translations = $this->nodeTranslationRepository->getByNodeId($node->getId());
            $translationData = [];
            foreach ($translations as $translation) {
                $storeId = $translation->getStoreId();
                if (isset($storeViewLabels[$storeId])) {
                    $translationData[] = [
                        'store_id' => $storeViewLabels[$storeId]['value'],
                        'value' => $translation->getTitle(),
                        'label' => $storeViewLabels[$storeId]['label']
                    ];
                }
            }

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
                'image_width' => $node->getImageWidth(),
                'image_height' => $node->getImageHeight(),
                'columns' => $this->renderNodeList($level + 1, $node->getId(), $data) ?: [],
                'selected_item_id' => $node->getSelectedItemId(),
                'customer_groups' => $node->getCustomerGroups(),
                'translations' => $translationData
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

    public function getCustomerGroups()
    {
        return $this->customerGroupsProvider->getAll();
    }

    /**
     * Get store views for translations
     *
     * @return array
     */
    public function getStoreViews()
    {
        $storeViews = [];
        $stores = $this->systemStore->getStoreCollection();
        $websites = $this->systemStore->getWebsiteCollection();
        $websiteNames = [];

        // Create website name lookup array
        foreach ($websites as $website) {
            $websiteNames[$website->getId()] = $website->getName();
        }

        foreach ($stores as $store) {
            if (!$store->isActive()) {
                continue;
            }

            $websiteName = isset($websiteNames[$store->getWebsiteId()])
                ? $websiteNames[$store->getWebsiteId()]
                : '';

            $storeViews[] = [
                'value' => $store->getId(),
                'label' => sprintf('%s -> %s', $websiteName, $store->getName())
            ];
        }

        return $storeViews;
    }
}
