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
     * @var null
     */
    private $templateDir = null;

    /**
     * @var array
     */
    private $templateList = [];

    public function __construct(
        Validator $validator,
        DriverFile $driverFile,
        Registry $registry,
        AssetRepository $assetRepo,
        RulePool $rulePool
    ) {
        $this->validator = $validator;
        $this->driverFile = $driverFile;
        $this->registry = $registry;
        $this->assetRepo = $assetRepo;
        $this->rulePool = $rulePool;
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
            return $this->setTemplateMap($menuId, $template, $template);
        }

        return $this->setTemplateMap($menuId, $newTemplate, $template);
    }

    /**
     * @param string $noteType
     * @return array
     */
    public function getCustomTemplateOptions($noteType)
    {
        return $this->getTemplateList($noteType);
    }

    /**
     * @param string $noteType
     * @return array
     * @throws FileSystemException
     */
    private function getTemplateList($noteType = '')
    {
        $result[] = [
            'label' => 'default',
            'id' => $noteType
        ];

        if (empty($noteType)) {
            return $result;
        }

        if (isset($this->templateList[$noteType])) {
            return $this->templateList[$noteType];
        }

        $templateDir = $this->getTemplateDir() . $noteType . DIRECTORY_SEPARATOR;
        if ($this->driverFile->isExists($templateDir)) {
            $files = $this->driverFile->readDirectory($templateDir);
            foreach ($files as $file) {
                if ($this->driverFile->isFile($file)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['phtml'])) {
                        $fileName = str_replace([$templateDir, '.' . $extension], '', $file);
                        $result[] = ['id' => $fileName];
                    }
                }
            }
        }
        $this->templateList[$noteType] = $result;

        return $this->templateList[$noteType];
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

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getTemplateDir()
    {
        if (!is_null($this->templateDir)) {
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
        $this->templateDir = '';
        foreach ($fallbackType->getPatternDirs($params) as $dir) {
            $templateDir = $dir . $customTemplatePath;
            if ($this->driverFile->isExists($templateDir)) {
                $this->templateDir = $templateDir;
                break;
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
