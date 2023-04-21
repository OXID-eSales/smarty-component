<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\Smarty\Module\Plugin\SmartyPluginDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/** @internal */
final class ShopConfigurationSmartyPluginDirectoryHandlerTest extends IntegrationTestCase
{
    public function setup(): void
    {
        parent::setUp();

        $modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($modulePath);
    }

    public function testHandleOnModuleActivationWillSaveMergedConfig(): void
    {
        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $moduleConfiguration = $configurationDao->get('test-module', 1);

        $settingHandler = $this
            ->get('oxid_esales.smarty.module.setup.smarty_plugin_directories_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );

        $smartyPluginDao = $this->get(SmartyPluginDaoInterface::class);
        $this->assertSame(['test-module' => [
            'SmartyPlugins/directory1',
            'SmartyPlugins/directory2',
        ]], $smartyPluginDao->getPluginDirectories(1));
    }

    public function testHandleOnModuleDeactivation(): void
    {
        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $moduleConfiguration = $configurationDao->get('test-module', 1);

        $settingHandler = $this
            ->get('oxid_esales.smarty.module.setup.smarty_plugin_directories_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );

        $settingHandler->handleOnModuleDeactivation(
            $moduleConfiguration,
            1
        );

        $smartyPluginDao = $this->get(SmartyPluginDaoInterface::class);
        $this->assertSame([], $smartyPluginDao->getPluginDirectories(1));
    }
}
