<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\Smarty\Module\Plugin\ModuleSmartyPluginsDataProvider;
use OxidEsales\Smarty\Module\Plugin\SmartyPluginDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class ModuleSmartyPluginsDataProviderTest extends TestCase
{
    public function testGetPlugins(): void
    {
        $dataProvider = new ModuleSmartyPluginsDataProvider(
            $this->getSmartyPluginsDataProviderMock(),
            $this->getSmartyPluginDaoMock(),
            $this->getContextMock(),
            $this->getModulePathResolver()
        );

        $settings = ['shopDir/testModuleDir', 'Smarty/Component/Plugin'];

        $this->assertEquals($settings, $dataProvider->getPlugins());
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
        $mock = $this
            ->getMockBuilder(SmartyPluginDaoInterface::class)
            ->getMock();

        $mock
            ->method('getPluginDirectories')
            ->willReturn(['testModule' => ['testModuleDir']]);

        return $mock;
    }

    private function getSmartyPluginsDataProviderMock(): SmartyPluginsDataProviderInterface
    {
        $mock = $this
            ->getMockBuilder(SmartyPluginsDataProviderInterface::class)
            ->getMock();

        $mock
            ->method('getPlugins')
            ->willReturn(['Smarty/Component/Plugin']);

        return $mock;
    }
}
