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
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Snowdog\Menu\Api\NodeTypeInterface;

/** @noinspection MagentoApiInspection */
abstract class AbstractNode extends Template implements NodeTypeInterface
{
    const NAME_CODE = 'node_name';
    const CLASSES_CODE = 'node_classes';

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
     * @inheritDoc
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->addNodeAttribute(self::NAME_CODE, 'Node name', 'wysiwyg');
        $this->addNodeAttribute(self::CLASSES_CODE, 'Node CSS classes', 'text');
    }

    /**
     * @inheritDoc
     */
    public abstract function fetchData(array $nodes);

    /**
     * @inheritDoc
     */
    public abstract function getHtml(int $nodeId, int $level);

    /**
     * @inheritDoc
     */
    public abstract function getAddButtonLabel();

    /**
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
     * @return array
     */
    public function getNodeAttributes()
    {
        return $this->nodeAttributes;
    }

    /**
     * @param $key
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
     * @param $key
     * @param $label
     * @param $type
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
     * @param $key
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
     * @return bool
     */
    public function isViewAllLinkAllowed()
    {
        return $this->viewAllLink;
    }
}
