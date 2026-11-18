<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Block\NodeType;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;
use Snowdog\Menu\Api\NodeTypeInterface;
use Snowdog\Menu\Model\TemplateResolver;

/** @noinspection MagentoApiInspection */
abstract class AbstractNode extends Template implements NodeTypeInterface
{
    public const NAME_CODE = 'node_name';
    public const CLASSES_CODE = 'node_classes';

    /**
     * @var string
     */
    protected $defaultTemplate;

    /**
     * @var string
     */
    protected $customTemplateFolder;

    /**
     * @var string
     */
    protected $nodeType;
    /**
     * Main node attributes
     *
     * @var DataObject[]
     */
    protected $nodeAttributes = [];
    /**
     * Determines whether a "View All" link item,
     * of the current parent node, could be added to menu.
     *
     * @var bool
     */
    protected $viewAllLink = true;

    /**
     * @var TemplateResolver
     */
    protected $templateResolver;

    /**
     * @inheritDoc
     */
    public function __construct(
        Template\Context $context,
        TemplateResolver $templateResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->addNodeAttribute(self::NAME_CODE, 'Node name', 'wysiwyg');
        $this->addNodeAttribute(self::CLASSES_CODE, 'Node CSS classes', 'text');
        $this->templateResolver = $templateResolver;
    }

    /**
     * Return the node type code.
     *
     * @return string
     */
    public function getNodeType()
    {
        if (!$this->nodeType) {
            return strtolower(__CLASS__);
        }

        return strtolower($this->nodeType);
    }

    /**
     * Return the available node attributes.
     *
     * @return array
     */
    public function getNodeAttributes()
    {
        return $this->nodeAttributes;
    }

    /**
     * Return a node attribute or an empty data object.
     *
     * @param string $key
     *
     * @return DataObject
     */
    public function getNodeAttribute($key)
    {
        if (array_key_exists($key, $this->nodeAttributes)) {
            return $this->nodeAttributes[$key];
        }

        return new DataObject();
    }

    /**
     * Register a node attribute.
     *
     * @param string $key
     * @param string $label
     * @param string $type
     */
    public function addNodeAttribute($key, $label, $type)
    {
        $data = [
            'id' => $key . '_' . $this->nodeType,
            'label' => $label,
            'type' => $type,
            'code' => $key
        ];
        $this->nodeAttributes[$key] = new DataObject($data);
    }

    /**
     * Remove a registered node attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    public function removeNodeAttribute($key)
    {
        if (array_key_exists($key, $this->nodeAttributes)) {
            unset($this->nodeAttributes[$key]);

            return true;
        }

        return false;
    }

    /**
     * Check whether the node type supports a view-all link.
     *
     * @return bool
     */
    public function isViewAllLinkAllowed()
    {
        return $this->viewAllLink;
    }

    /**
     * Return the custom template folder.
     *
     * @return string
     */
    public function getCustomTemplateFolder()
    {
        return $this->customTemplateFolder;
    }

    /**
     * Resolve the node template before rendering.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $template = $this->templateResolver->getMenuTemplate(
            $this,
            $this->getMenuCode(),
            $this->defaultTemplate,
            $this->getId()
        );
        $this->_template = $template;

        return parent::_toHtml();
    }
}
