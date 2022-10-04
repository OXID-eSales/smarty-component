<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\Smarty\Configuration\SmartyResourcesDataProvider;
use OxidEsales\Smarty\Extension\ResourcePluginInterface;
use PHPUnit\Framework\TestCase;

class SmartyResourcesDataProviderTest extends TestCase
{
    public function testGetSmartyResources(): void
    {
        $pluginMock = $this->getMockBuilder(ResourcePluginInterface::class)->getMock();
        $datProvider = new SmartyResourcesDataProvider($pluginMock);

        $settings = ['ox' => [
            $pluginMock,
            'getTemplate',
            'getTimestamp',
            'getSecure',
            'getTrusted'
        ]
        ];

        $this->assertEquals($settings, $datProvider->getResources());
    }
}
