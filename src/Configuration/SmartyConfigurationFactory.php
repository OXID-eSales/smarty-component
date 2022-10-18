<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Configuration;

use OxidEsales\Smarty\SmartyContextInterface;

class SmartyConfigurationFactory implements SmartyConfigurationFactoryInterface
{
    public function __construct(
        private SmartyContextInterface $context,
        private SmartySettingsDataProviderInterface $settingsDataProvider,
        private SmartySecuritySettingsDataProviderInterface $securitySettingsDataProvider,
        private SmartyResourcesDataProviderInterface $resourcesDataProvider,
        private SmartyPrefiltersDataProviderInterface $prefiltersDataProvider,
        private SmartyPluginsDataProviderInterface $pluginsDataProvider
    ) {
    }

    /**
     * @return SmartyConfigurationInterface
     */
    public function getConfiguration(): SmartyConfigurationInterface
    {
        $smartyConfiguration = new SmartyConfiguration();
        $smartyConfiguration->setSettings($this->settingsDataProvider->getSettings());
        $smartyConfiguration->setTemplateCompilePath($this->context->getTemplateCompileDirectory());
        if ($this->context->getTemplateSecurityMode()) {
            $smartyConfiguration->setSecuritySettings($this->securitySettingsDataProvider->getSecuritySettings());
        }
        $smartyConfiguration->setResources($this->resourcesDataProvider->getResources());
        $smartyConfiguration->setPrefilters($this->prefiltersDataProvider->getPrefilterPlugins());
        $smartyConfiguration->setPlugins($this->pluginsDataProvider->getPlugins());

        return $smartyConfiguration;
    }
}
