<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty;

use OxidEsales\Smarty\Bridge\SmartyEngineBridge;
use OxidEsales\Smarty\Configuration\SmartyConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

class SmartyEngineFactory implements TemplateEngineFactoryInterface
{
    public function __construct(
        private SmartyBuilderInterface $smartyBuilder,
        private SmartyConfigurationInterface $smartyConfiguration,
        private TemplateFileResolverInterface $templateFileResolver,
        private SmartyContextInterface $context
    ) {
    }

    /**
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface
    {
        $smarty = $this->smartyBuilder
            ->setTemplateCompilePath($this->smartyConfiguration->getTemplateCompilePath())
            ->setSettings($this->smartyConfiguration->getSettings())
            ->setSecuritySettings($this->smartyConfiguration->getSecuritySettings())
            ->registerPlugins($this->smartyConfiguration->getPlugins())
            ->registerPrefilters($this->smartyConfiguration->getPrefilters())
            ->registerResources($this->smartyConfiguration->getResources())
            ->getSmarty();

        // TODO Event for smarty object configuration

        return new SmartyEngine($smarty, new SmartyEngineBridge(), $this->templateFileResolver, $this->context);
    }
}
