<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Plugin;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModuleSmartyPluginsDataProvider implements SmartyPluginsDataProviderInterface
{
    public function __construct(
        private SmartyPluginsDataProviderInterface $dataProvider,
        private SmartyPluginDaoInterface $smartyPluginDao,
        private ContextInterface $context,
        private ModulePathResolverInterface $metadataResolver
    )
    {
    }

    public function getPlugins(): array
    {
        return array_merge($this->getModuleSmartyPluginPaths($this->getModuleSmartyPluginDirectories()), $this->dataProvider->getPlugins());
    }

    /**
     * @return array
     */
    private function getModuleSmartyPluginDirectories(): array
    {
        return $this->smartyPluginDao->getPluginDirectories($this->context->getCurrentShopId());
    }

    private function getModuleSmartyPluginPaths(array $pluginSettings): array
    {
        $pluginDirectories = [];
        foreach ($pluginSettings as $moduleId => $pluginSetting) {
            $modulePath = $this->metadataResolver->getFullModulePathFromConfiguration($moduleId, $this->context->getCurrentShopId());
            foreach ($pluginSetting as $directory){
                $pluginDirectories[] = $modulePath . DIRECTORY_SEPARATOR . $directory;
            }
        }
        return $pluginDirectories;
    }
}
