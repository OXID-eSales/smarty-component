<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPrefiltersDataProvider;
use OxidEsales\Smarty\SmartyContextInterface;

class SmartyPrefiltersDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSmartyPrefilters()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $dataProvider = new SmartyPrefiltersDataProvider($smartyContextMock);

        $settings = [
            'smarty_prefilter_oxblock' => 'testShopPath/Internal/Framework/Smarty/Plugin/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testShopPath/Internal/Framework/Smarty/Plugin/prefilter.oxtpldebug.php',
        ];

        $this->assertEquals($settings, $dataProvider->getPrefilterPlugins());
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(true);

        $smartyContextMock
            ->method('getSourcePath')
            ->willReturn('testShopPath');

        return $smartyContextMock;
    }
}
