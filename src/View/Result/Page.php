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
     * @param string $template,
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
    }

    protected function getStaticDirectory($url)
    {
        return join('/', array('Magento_Theme/static', $url));
    }

    public function getStaticFile($url)
    {
        $asset = $this->assetRepo->createAsset(
            $this->getStaticDirectory($url)
        );

        return $asset->getUrl();
    }

    public function getLanguageCode() {
        $haystack = $this->getLocaleCode();

        return strstr($haystack, '_', true);
    }

    public function getLocaleCode()
    {
        return $this->localeResolver->getLocale();
    }
}
