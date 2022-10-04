<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class ModuleSmartyPluginDirectoryTest
 */
class ModuleSmartyPluginDirectoriesTest extends UnitTestCase
{
    private $container;

    public function setup(): void
    {
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        $this->activateTestModule();
    }

    public function tearDown(): void
    {
        $this->deactivateTestModule();

        $this->container
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
    }

    /**
     * Smarty should know about the smarty plugin directories of the modules being activated.
     */
    public function testModuleSmartyPluginDirectoryIsIncludedOnModuleActivation()
    {
        $templating = $this->container->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
        $this->assertTrue(
            $this->isPathInSmartyDirectories($templating->getTemplateEngine(), 'Smarty/PluginDirectory1WithMetadataVersion21')
        );

        $this->assertTrue(
            $this->isPathInSmartyDirectories($templating->getTemplateEngine(), 'Smarty/PluginDirectory2WithMetadataVersion21')
        );
    }

    public function testSmartyPluginDirectoriesOrder()
    {
        $templating = $this->container->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
        $this->assertModuleSmartyPluginDirectoriesFirst($templating->getTemplateEngine()->plugins_dir);
        $this->assertShopSmartyPluginDirectorySecond($templating->getTemplateEngine()->plugins_dir);
    }

    private function assertModuleSmartyPluginDirectoriesFirst($directories)
    {
        $this->assertStringContainsString(
            'Smarty/PluginDirectory1WithMetadataVersion21',
            $directories[0]
        );

        $this->assertStringContainsString(
            'Smarty/PluginDirectory2WithMetadataVersion21',
            $directories[1]
        );
    }

    private function assertShopSmartyPluginDirectorySecond($directories)
    {
        $this->assertStringContainsString(
            'Internal/Framework/Smarty/Plugin',
            $directories[2]
        );
    }

    private function isPathInSmartyDirectories($smarty, $path)
    {
        foreach ($smarty->plugins_dir as $directory) {
            if (strpos($directory, $path)) {
                return true;
            }
        }

        return false;
    }

    private function activateTestModule()
    {
        $id = 'with_metadata_v21';
        $package = new OxidEshopPackage(__DIR__ . '/Fixtures/' . $id);

        $this->container->get(ModuleInstallerInterface::class)
            ->install($package);

        $this->container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('with_metadata_v21', Registry::getConfig()->getShopId());
    }

    private function deactivateTestModule()
    {
        $this->container
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate('with_metadata_v21', Registry::getConfig()->getShopId());
    }
}
