<?php

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Snowdog\Menu\Model\TemplateResolver;
use Snowdog\Menu\Model\NodeType\Category as ModelCategory;

class CategoryChild extends Category
{
    /**
     * @var string
     */
    protected $defaultTemplate = 'menu/node_type/category_child.phtml';
    /**
     * @var string
     */
    protected $nodeType = 'category_child';
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ModelCategory $categoryModel
     * @param TemplateResolver $templateResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ModelCategory $categoryModel,
        TemplateResolver $templateResolver,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $categoryModel, $templateResolver, $data);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function getNodeCacheKeyInfo()
    {
        $info = [
            'module_' . $this->getRequest()->getModuleName(),
            'controller_' . $this->getRequest()->getControllerName(),
            'route_' . $this->getRequest()->getRouteName(),
            'action_' . $this->getRequest()->getActionName()
        ];
        $category = $this->getCurrentCategory();
        if ($category) {
            $info[] = 'category-child_' . $category->getId();
        }

        return $info;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __("Category Child");
    }

    /**
     * Get Visible Children
     *
     * @return array
     */
    public function getVisibleChildren()
    {
        $categoryId = $this->getContent();
        $category = $this->categoryRepository->get($categoryId);
        $visibleChildren = [];
        $children = $category->getChildrenCategories();
        if (is_object($children)) {
            $children->clear();
            $children->addAttributeToSelect('include_in_menu');
            $children->load();
        }
        foreach ($children as $child) {
            if ($child->getIsActive() && $child->getIncludeInMenu()) {
                $visibleChildren[] = $child;
            }
        }

        return $visibleChildren;
    }
}
