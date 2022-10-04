<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class SmartyPluginsDataProviderTest extends IntegrationTestCase
{
    public function testGetPlugins()
    {
        $this->markTestIncomplete('First refactor module plugin functionality');
        $dataProvider = $this->get(SmartyPluginsDataProviderInterface::class);

        $settings = ['testModuleDir', 'testShopPath/Core/Smarty/Plugin'];

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
