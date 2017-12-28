<?php
/**
 * @author      Dawid Czaja <dawid.czaja@snow.dog>.
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\File\Validator;

class TemplateResolver
{
    /**
     * @var array
     */
    private $templateMap = [];

    /**
     * TemplateResolver constructor.
     * @param Validator $validator
     */
    public function __construct(
        Validator $validator
    ) {
        $this->validator = $validator;
    }


    /**
     * @param Template $block
     * @param string $menuId
     * @param string $template
     * @return string
     */
    public function getMenuTemplate($block, $menuId, $template)
    {
        if (isset($this->templateMap[$menuId . '-' . $template])) {
            return $this->templateMap[$menuId . '-' . $template];
        }

        $templateArr = explode('::', $template);
        if (isset($templateArr[1])) {
            $newTemplate = $templateArr[0] . '::' . $menuId . DIRECTORY_SEPARATOR . $templateArr[1];
        } else {
            $newTemplate = $menuId . DIRECTORY_SEPARATOR . $template;
        }

        if (!$this->validator->isValid($block->getTemplateFile($newTemplate))) {
            return $this->setTemplateMap($menuId, $template, $template);
        }

        return $this->setTemplateMap($menuId, $newTemplate, $template);
    }

    /**
     * @param string $menuId
     * @param string $template
     * @param string $oldTemplate
     * @return string
     */
    private function setTemplateMap($menuId, $template, $oldTemplate)
    {
        return $this->templateMap[$menuId . '-' . $oldTemplate] = $template;
    }
}
