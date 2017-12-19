<?php
/**
 * Snowdog
 *
 * @author      Dawid Czaja <dawid.czaja@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model;

use Magento\Framework\View\Element\Template;

class TemplateResolver
{
    protected $templateMap;

    /**
     * @param Template $block
     * @param string $menuId
     * @param string $oldTemplate
     * @return string
     */
    public function getMenuTemplate($block, $menuId, $oldTemplate)
    {
        if (isset($this->templateMap[$menuId . '-' . $oldTemplate])) {
            return $this->templateMap[$menuId . '-' . $oldTemplate];
        }

        $template = explode('::', $oldTemplate);
        if (isset($template[1])) {
            $newTemplate = $template[0] . '::' . $menuId . DIRECTORY_SEPARATOR . $template[1];
        } else {
            $newTemplate = $menuId . DIRECTORY_SEPARATOR . $oldTemplate;
        }

        if (!file_exists($block->getTemplateFile($newTemplate))) {
            return $this->setTemplateMap($menuId, $oldTemplate, $oldTemplate);
        }

        return $this->setTemplateMap($menuId, $newTemplate, $oldTemplate);
    }

    /**
     * @param string $menuId
     * @param string $template
     * @param string $oldTemplate
     * @return string
     */
    protected function setTemplateMap($menuId, $template, $oldTemplate)
    {
        return $this->templateMap[$menuId . '-' . $oldTemplate] = $template;
    }
}
