<?php
/**
 * Snowdog
 *
 * @author      Paweł Pisarek <pawel.pisarek@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Block\Element;

class TextArea extends \Magento\Framework\Data\Form\Element\Textarea
{
    /**
     * @inheritDoc
     */
    public function getElementHtml()
    {
        $this->addClass('textarea admin__control-textarea');
        $html = '<textarea id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" '
            . $this->serialize($this->getHtmlAttributes())
            . $this->serializeSingleQuoted($this->getSingleQuoteHtmlAttributes())
            . $this->_getUiId() . ' >';
        $html .= $this->getEscapedValue();
        $html .= "</textarea>";
        $html .= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * @inheritDoc
     */
    public function getHtmlId()
    {
        return $this->getData('html_id');
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Return the HTML attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return [
            'title',
            'class',
            'style',
            'type'
        ];
    }

    /**
     * Return the HTML attributes
     *
     * @return string[]
     */
    public function getSingleQuoteHtmlAttributes()
    {
        return [
            'data-mage-init'
        ];
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setDataMageInit($value)
    {
        $this->setData('data-mage-init', $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function serializeSingleQuoted($attributes = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = "'")
    {
        return parent::serialize(
            $attributes, $valueSeparator, $fieldSeparator, $quote
        );
    }
}