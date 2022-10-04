<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPluginsDataProvider;
use OxidEsales\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class SmartyPluginsDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $contextMock = $this->getContextMock();

        $dataProvider = new SmartyPluginsDataProvider($contextMock);

        $settings = ['testShopPath/Internal/Framework/Smarty/Plugin'];

        $this->assertEquals($settings, $dataProvider->getPlugins());
    }

    private function getContextMock(): BasicContextInterface
    {
        $contextMock = $this
            ->getMockBuilder(BasicContextInterface::class)
            ->getMock();

        $contextMock
            ->method('getCommunityEditionSourcePath')
            ->willReturn('testShopPath');

        return $contextMock;
    }
}
