<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\Smarty\Module\Plugin\ModuleSmartyPluginsDataProvider;
use OxidEsales\Smarty\Configuration\SmartyPluginsDataProvider;
use OxidEsales\Smarty\Module\Plugin\SmartyPluginDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModuleSmartyPluginsDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPlugins()
    {
        $dataProvider = new ModuleSmartyPluginsDataProvider(
            new SmartyPluginsDataProvider($this->getBasicContextMock()),
            $this->getSmartyPluginDaoMock(),
            $this->getContextMock(),
            $this->getModulePathResolver()
        );

        $settings = ['shopDir/testModuleDir', 'testShopPath/Internal/Framework/Smarty/Plugin'];

        $this->assertEquals($settings, $dataProvider->getPlugins());
    }

    private function getBasicContextMock(): BasicContextInterface
    {
        $contextMock = $this
            ->getMockBuilder(BasicContextInterface::class)
            ->getMock();

        $contextMock
            ->method('getCommunityEditionSourcePath')
            ->willReturn('testShopPath');

        return $contextMock;
    }

    private function getContextMock(): ContextInterface
    {
        $contextMock = $this
            ->getMockBuilder(ContextInterface::class)
            ->getMock();

        $contextMock
            ->method('getCurrentShopId')
            ->willReturn(1);

        return $contextMock;
    }

    private function getModulePathResolver(): ModulePathResolverInterface
    {
        $mock = $this
            ->getMockBuilder(ModulePathResolverInterface::class)
            ->getMock();

        $mock
            ->method('getFullModulePathFromConfiguration')
            ->willReturn('shopDir');

        return $mock;
    }

    private function getSmartyPluginDaoMock(): SmartyPluginDaoInterface
    {
        $shopAdapterMock = $this
            ->getMockBuilder(SmartyPluginDaoInterface::class)
            ->getMock();

        $shopAdapterMock
            ->method('getPluginDirectories')
            ->willReturn(['testModule' => ['testModuleDir']]);

        return $shopAdapterMock;
    }
}
