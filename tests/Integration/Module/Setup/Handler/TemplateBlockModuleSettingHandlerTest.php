<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class TemplateBlockModuleSettingHandlerTest extends IntegrationTestCase
{
    public function setup(): void
    {
        parent::setUp();

        $modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($modulePath);
    }

    public function testHandlingOnModuleActivation(): void
    {
        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $moduleConfiguration = $configurationDao->get('test-module', 1);

        $settingHandler = $this->get('oxid_esales.smarty.module.setup.template_block_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );
        $templateBlockDao = $this->get(TemplateBlockExtensionDaoInterface::class);
        $this->assertTrue($templateBlockDao->exists(['test-module'], 1));
        $this->assertCount(
            1,
            $templateBlockDao->getExtensionsByTemplateName('template_1.tpl', ['test-module'], 1, ['theme_id'])
        );
        $this->assertCount(
            1,
            $templateBlockDao->getExtensionsByTemplateName('template_2.tpl', ['test-module'], 1)
        );
    }

    public function testHandlingOnModuleDeactivation(): void
    {
        $configurationDao = $this->get(ModuleConfigurationDaoInterface::class);
        $moduleConfiguration = $configurationDao->get('test-module', 1);

        $settingHandler = $this->get('oxid_esales.smarty.module.setup.template_block_module_setting_handler');
        $settingHandler->handleOnModuleActivation(
            $moduleConfiguration,
            1
        );

        $templateBlockDao = $this->get(TemplateBlockExtensionDaoInterface::class);
        $this->assertTrue($templateBlockDao->exists(['test-module'], 1));

        $settingHandler->handleOnModuleDeactivation(
            $moduleConfiguration,
            1
        );
        $templateBlockDao = $this->get(TemplateBlockExtensionDaoInterface::class);
        $this->assertFalse($templateBlockDao->exists(['test-module'], 1));
    }
}
