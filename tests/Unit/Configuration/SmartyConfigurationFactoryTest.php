<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\Smarty\Configuration\SmartyPrefiltersDataProviderInterface;
use OxidEsales\Smarty\Configuration\SmartyResourcesDataProviderInterface;
use OxidEsales\Smarty\Configuration\SmartySecuritySettingsDataProviderInterface;
use OxidEsales\Smarty\Configuration\SmartySettingsDataProviderInterface;
use OxidEsales\Smarty\SmartyContextInterface;
use OxidEsales\Smarty\Configuration\SmartyConfigurationFactory;

class SmartyConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $factory = new SmartyConfigurationFactory(
            $this->getSmartyContextMock(false),
            $this->getSmartySettingsDataProviderMock(),
            $this->getSmartySecuritySettingsDataProviderMock(),
            $this->getSmartyResourcesDataProviderMock(),
            $this->getSmartyPrefiltersDataProviderMock(),
            $this->getSmartyPluginsDataProviderMock()
        );
        $configuration = $factory->getConfiguration();

        $this->assertSame(['testSetting'], $configuration->getSettings());
        $this->assertSame([], $configuration->getSecuritySettings());
        $this->assertSame(['testResources'], $configuration->getResources());
        $this->assertSame(['testPlugins'], $configuration->getPlugins());
        $this->assertSame(['testPrefilters'], $configuration->getPrefilters());
    }

    public function testGetConfigurationWithSecuritySettingsOn()
    {
        $factory = new SmartyConfigurationFactory(
            $this->getSmartyContextMock(true),
            $this->getSmartySettingsDataProviderMock(),
            $this->getSmartySecuritySettingsDataProviderMock(),
            $this->getSmartyResourcesDataProviderMock(),
            $this->getSmartyPrefiltersDataProviderMock(),
            $this->getSmartyPluginsDataProviderMock()
        );
        $configuration = $factory->getConfiguration();

        $this->assertSame(['testSetting'], $configuration->getSettings());
        $this->assertSame(['testSecuritySetting'], $configuration->getSecuritySettings());
        $this->assertSame(['testResources'], $configuration->getResources());
        $this->assertSame(['testPlugins'], $configuration->getPlugins());
        $this->assertSame(['testPrefilters'], $configuration->getPrefilters());
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        return $smartyContextMock;
    }

    private function getSmartySettingsDataProviderMock(): SmartySettingsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartySettingsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getSettings')
            ->willReturn(['testSetting']);

        return $smartyContextMock;
    }

    private function getSmartySecuritySettingsDataProviderMock(): SmartySecuritySettingsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartySecuritySettingsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getSecuritySettings')
            ->willReturn(['testSecuritySetting']);

        return $smartyContextMock;
    }

    private function getSmartyResourcesDataProviderMock(): SmartyResourcesDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyResourcesDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getResources')
            ->willReturn(['testResources']);

        return $smartyContextMock;
    }

    private function getSmartyPluginsDataProviderMock(): SmartyPluginsDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyPluginsDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getPlugins')
            ->willReturn(['testPlugins']);

        return $smartyContextMock;
    }

    private function getSmartyPrefiltersDataProviderMock(): SmartyPrefiltersDataProviderInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyPrefiltersDataProviderInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getPrefilterPlugins')
            ->willReturn(['testPrefilters']);

        return $smartyContextMock;
    }
}
