<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPrefiltersDataProvider;
use OxidEsales\Smarty\SmartyContextInterface;

class SmartyPrefiltersDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSmartyPrefilters()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $settings = (new SmartyPrefiltersDataProvider($smartyContextMock))->getPrefilterPlugins();

        $this->assertEquals('prefilter.oxblock.php', basename($settings['smarty_prefilter_oxblock']));
        $this->assertTrue(file_exists($settings['smarty_prefilter_oxblock']));
        $this->assertEquals('prefilter.oxtpldebug.php', basename($settings['smarty_prefilter_oxtpldebug']));
        $this->assertTrue(file_exists($settings['smarty_prefilter_oxtpldebug']));
    }

    private function getSmartyContextMock(): SmartyContextInterface
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
