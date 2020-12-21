<?php
/**
 * ScandiPWA - Progressive Web App for Magento
 *
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See LICENSE for license details.
 *
 * @license OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * @link https://github.com/scandipwa/quote-graphql
 */

namespace ScandiPWA\Locale\View\Result;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Page\Config\RendererFactory;
use Magento\Framework\View\Page\Layout\Reader;
use ScandiPWA\Router\View\Result\Page as OriginalPage;

class Page extends OriginalPage
{
    /**
     * @var Resolver
     */
    private $localeResolver;
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Page constructor.
     * @param Resolver $localeResolver
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     * @param ReaderPool $layoutReaderPool
     * @param InlineInterface $translateInline
     * @param BuilderFactory $layoutBuilderFactory
     * @param GeneratorPool $generatorPool
     * @param RendererFactory $pageConfigRendererFactory
     * @param Reader $pageLayoutReader
     * @param string $template ,
     * @param DirectoryList $directoryList
     * @param bool $isIsolated
     * @param EntitySpecificHandlesList|null $entitySpecificHandlesList
     * @param null $action
     * @param array $rootTemplatePool
     */
    public function __construct(
        Resolver $localeResolver,
        Context $context,
        LayoutFactory $layoutFactory,
        ReaderPool $layoutReaderPool,
        InlineInterface $translateInline,
        BuilderFactory $layoutBuilderFactory,
        GeneratorPool $generatorPool,
        RendererFactory $pageConfigRendererFactory,
        Reader $pageLayoutReader,
        string $template,
        DirectoryList $directoryList,
        $isIsolated = false,
        EntitySpecificHandlesList $entitySpecificHandlesList = null,
        $action = null,
        $rootTemplatePool = []
    ) {
        parent::__construct(
            $context,
            $layoutFactory,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
            $generatorPool,
            $pageConfigRendererFactory,
            $pageLayoutReader,
            $template,
            $isIsolated,
            $entitySpecificHandlesList,
            $action,
            $rootTemplatePool
        );

        $this->localeResolver = $localeResolver;
        $this->directoryList = $directoryList;
    }

    public function getLanguageCode() {
        $haystack = $this->getLocaleCode();

        return strstr($haystack, '_', true);
    }

    public function getLocaleCode()
    {
        return $this->localeResolver->getLocale();
    }

    protected function getJsBundleLocation() {
        // Common directory for all locales
        $staticViewDirectory = $this->directoryList->getPath(DirectoryList::STATIC_VIEW);

        // Specific directory for each locale
        $staticViewFileContextPathForLocale = $this->assetRepo->getStaticViewFileContext()->getPath();

        // Always need to use en_US locale for locating bundles
        $defaultLocale = "en_US";
        $pattern = "/\/{$this->getLocaleCode()}$/";
        $replacement = "/$defaultLocale";

        $staticViewFileContextPathForDefaultLocale = preg_replace(
            $pattern,
            $replacement,
            $staticViewFileContextPathForLocale
        );

        return join('/', array(
            $staticViewDirectory,
            $staticViewFileContextPathForDefaultLocale,
            'Magento_Theme',
            'static',
            'js'
        ));
    }

    protected function getLocaleChunkName() {
        $locale = $this->getLocaleCode();
        $jsBundleLocation = $this->getJsBundleLocation();

        try {
            $jsFileList = scandir($jsBundleLocation);
        } catch (\Throwable $error) {
            return null;
        }

        $necessaryFileRegExp = "/$locale\\.[^.]+\\.chunk\\.js$/";
        return current(array_filter(
            $jsFileList,
            function($filename) use ($necessaryFileRegExp) {
                return preg_match($necessaryFileRegExp, $filename);
            }
        ));
    }

    public function getLocaleChunkUrl() {
        $chunkName = $this->getLocaleChunkName();
        if (!$chunkName) {
            return null;
        }

        $assetPath = join('/', array(
            'Magento_Theme',
            'static',
            'js',
            $chunkName
        ));

        try {
            $asset = $this->assetRepo->createAsset($assetPath, ['locale' => 'en_US']);
            return $asset->getUrl();
        } catch (LocalizedException $e) {
            return null;
        }
    }
}
