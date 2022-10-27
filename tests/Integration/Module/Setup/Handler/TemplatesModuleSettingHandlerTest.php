<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Smarty\Module\Template\TemplateDaoInterface;

final class TemplatesModuleSettingHandlerTest extends IntegrationTestCase
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

        $settingHandler = $this->get('oxid_esales.smarty.module.setup.templates_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );

        $templateDao = $this->get(TemplateDaoInterface::class);
        $this->assertSame(['test-module' => [
            'vendor1_controller_routing.tpl' => 'TestModule/views/tpl/vendor1_controller_routing.tpl'
        ]], $templateDao->getTemplates(1));
    }

    public function testHandleOnModuleDeactivationWillSaveCleanedConfig(): void
    {
        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $moduleConfiguration = $configurationDao->get('test-module', 1);

        $settingHandler = $this->get('oxid_esales.smarty.module.setup.templates_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );

        $settingHandler->handleOnModuleDeactivation(
            $moduleConfiguration,
            1
        );

        $templateDao = $this->get(TemplateDaoInterface::class);
        $this->assertSame([], $templateDao->getTemplates(1));
    }
}
