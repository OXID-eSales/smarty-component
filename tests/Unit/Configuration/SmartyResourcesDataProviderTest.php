<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\Smarty\Configuration\SmartyResourcesDataProvider;
use OxidEsales\Smarty\Extension\ResourcePluginInterface;
use PHPUnit\Framework\TestCase;

final class SmartyResourcesDataProviderTest extends TestCase
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
