<?php
/**
 * @author      Dawid Czaja <dawid.czaja@snow.dog>.
 * @copyright   Copyright Snowdog (http://snow.dog)
 */

namespace Snowdog\Menu\Model;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\File\Validator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

class TemplateResolver
{
    /**
     * @var array
     */
    private $templateMap = [];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var DriverFile
     */
    private $driverFile;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var null
     */
    private $templateDir = null;

    private $templateList = [];

    public function __construct(
        Validator $validator,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ThemeProviderInterface $themeProvider,
        DriverFile $driverFile,
        Filesystem $filesystem
    ) {
        $this->validator = $validator;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->themeProvider = $themeProvider;
        $this->driverFile = $driverFile;
        $this->filesystem = $filesystem;
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

        $templateDir = $this->getThemeDir() . $noteType . DIRECTORY_SEPARATOR;
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
     * @return string
     * @throws NoSuchEntityException
     */
    private function getThemeDir()
    {
        if (!is_null($this->templateDir)) {
            return $this->templateDir;
        }

        $themeId = $this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        /** @var ThemeInterface $theme */
        $theme = $this->themeProvider->getThemeById($themeId);
        $themeFullPath = $theme->getFullPath();

        $appPath = $this->filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath();
        $customTemplatePath = '/Snowdog_Menu/templates/menu/custom/';
        $this->templateDir = $appPath . 'design/' . $themeFullPath . $customTemplatePath;

        return $this->templateDir;
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
