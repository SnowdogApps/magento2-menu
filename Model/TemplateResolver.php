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
    /**
     * @param Template $block
     * @param string $menuId
     * @param string $oldTemplate
     * @return string
     */
    public function getMenuTemplate($block, $menuId, $oldTemplate)
    {
        $template = explode('::', $oldTemplate);
        if (isset($template[1])) {
            $newTemplate = $template[0] . '::' . $menuId . DIRECTORY_SEPARATOR . $template[1];
        } else {
            $newTemplate = $menuId . DIRECTORY_SEPARATOR . $oldTemplate;
        }

        if (!file_exists($block->getTemplateFile($newTemplate))) {
            return $oldTemplate;
        }

        return $newTemplate;
    }
}
