<?php
/**
 * Snowdog
 *
 * @author      Dawid Czaja <dawid.czaja@snow.dog>.
 * @category
 * @package
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Block;

use Magento\Framework\View\Element\Template;

class TemplateResolver extends Template
{
    /**
     * @param string $menuId
     * @param string $oldTemplate
     * @return string
     */
    public function getMenuTemplate($menuId, $oldTemplate)
    {
        $template = explode('::', $oldTemplate);
        if (isset($template[1])) {
            $newTemplate = $template[0] . '::' . $menuId . DIRECTORY_SEPARATOR . $template[1];
        } else {
            $newTemplate = $menuId . DIRECTORY_SEPARATOR . $oldTemplate;
        }

        if (!file_exists($this->getTemplateFile($newTemplate))) {
            return $oldTemplate;
        }

        return $newTemplate;
    }
}
