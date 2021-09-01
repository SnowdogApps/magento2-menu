<?php
/**
 * @author      Dawid Czaja <dawid.czaja@snow.dog>.
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\File\Validator;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Design\Fallback\RulePool;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

class TemplateResolver
{
    const MODULE_NAME = 'Snowdog_Menu';

    /**
     * @var array
     */
    private $templateMap = [];

    /**
     * @var DriverFile
     */
    private $driverFile;

    /**
     * @var IoFile
     */
    private $ioFile;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var RulePool
     */
    private $rulePool;

    /**
     * @var array
     */
    private $templateDir = [];

    /**
     * @var array
     */
    private $templateList = [];

    public function __construct(
        Validator $validator,
        DriverFile $driverFile,
        IoFile $ioFile,
        Registry $registry,
        AssetRepository $assetRepo,
        RulePool $rulePool
    ) {
        $this->validator = $validator;
        $this->driverFile = $driverFile;
        $this->ioFile = $ioFile;
        $this->registry = $registry;
        $this->assetRepo = $assetRepo;
        $this->rulePool = $rulePool;
    }

    /**
     * @param Template $block
     * @param string $menuId
     * @param string $template
     * @param int|null $nodeId
     * @return string
     */
    public function getMenuTemplate($block, $menuId, $template, $nodeId = null)
    {
        $mapId = $menuId . '-' . ($nodeId ? $nodeId . '-' : '') . $template;
        if (isset($this->templateMap[$mapId])) {
            return $this->templateMap[$mapId];
        }

        $templateArr = explode('::', $template);
        if ($block->getCustomTemplate()) {
            $newTemplate = $menuId
                . DIRECTORY_SEPARATOR
                . $block->getCustomTemplateFolder()
                . $block->getCustomTemplate()
                . '.phtml';
        } elseif (isset($templateArr[1])) {
            $newTemplate = $templateArr[0] . '::' . $menuId . DIRECTORY_SEPARATOR . $templateArr[1];
        } else {
            $newTemplate = $menuId . DIRECTORY_SEPARATOR . $template;
        }

        if (!$this->validator->isValid($block->getTemplateFile($newTemplate))) {
            return $this->setTemplateMap($menuId, $template, $template, $nodeId);
        }

        return $this->setTemplateMap($menuId, $newTemplate, $template, $nodeId);
    }

    /**
     * @param string $nodeType
     * @param string $defaultTemplateLabel
     * @return array
     */
    public function getCustomTemplateOptions($nodeType, $defaultTemplateLabel = '')
    {
        return $this->getTemplateList($nodeType, $defaultTemplateLabel);
    }

    /**
     * @param string $nodeType
     * @param string $defaultTemplateLabel
     * @return array
     * @throws FileSystemException
     */
    private function getTemplateList($nodeType = '', $defaultTemplateLabel = '')
    {
        $result[] = [
            'label' => $defaultTemplateLabel ?: 'default',
            'id' => $nodeType
        ];

        if (empty($nodeType)) {
            return $result;
        }

        if (isset($this->templateList[$nodeType])) {
            return $this->templateList[$nodeType];
        }

        foreach ($this->getTemplateDir() as $themeDir) {
            $themeDir .= $nodeType . DIRECTORY_SEPARATOR;
            if (!$this->driverFile->isExists($themeDir)) {
                continue;
            }

            $files = $this->driverFile->readDirectory($themeDir);
            foreach ($files as $file) {
                if (!$this->driverFile->isFile($file)) {
                    continue;
                }

                $extension = strtolower($this->ioFile->getPathInfo($file)['extension']);
                if ($extension !== 'phtml') {
                    continue;
                }

                $fileName = str_replace([$themeDir, '.' . $extension], '', $file);
                if (!in_array($fileName, array_column($result, 'id'))) {
                    $result[] = ['id' => $fileName];
                }
            }
        }

        $this->templateList[$nodeType] = $result;

        return $this->templateList[$nodeType];
    }

    /**
     * @param string $menuId
     * @param string $template
     * @param string $oldTemplate
     * @param int|null $nodeId
     * @return string
     */
    private function setTemplateMap($menuId, $template, $oldTemplate, $nodeId = null)
    {
        $mapId = $menuId . '-' . ($nodeId ? $nodeId . '-' : '') . $oldTemplate;
        return $this->templateMap[$mapId] = $template;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    private function getTemplateDir()
    {
        if ($this->templateDir) {
            return $this->templateDir;
        }

        $menuIdentifier = $this->getMenuIdentifier();
        $params = ['module' => self::MODULE_NAME, 'area' => 'frontend'];
        $this->assetRepo->updateDesignParams($params);
        $fallbackType = $this->rulePool->getRule(RulePool::TYPE_FILE);

        $params = [
            'area' => $params['area'],
            'theme' => $params['themeModel'],
            'locale' => $params['locale'],
            'module_name' => $params['module']
        ];

        $menuIdentifier = $this->getMenuIdentifier();
        $customTemplatePath = '/templates/' . $menuIdentifier . '/menu/custom/';

        foreach ($fallbackType->getPatternDirs($params) as $dir) {
            $templateDir = $dir . $customTemplatePath;
            if ($this->driverFile->isExists($templateDir)) {
                $this->templateDir[] = $templateDir;
            }
        }

        return $this->templateDir;
    }

    /**
     * @return string
     */
    private function getMenuIdentifier()
    {
        $menu = $this->registry->registry('snowmenu_menu');
        if (!$menu) {
            return '';
        }

        return $menu->getIdentifier();
    }
}
