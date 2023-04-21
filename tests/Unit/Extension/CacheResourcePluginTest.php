<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Extension;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\Smarty\SmartyContextInterface;
use PHPUnit\Framework\TestCase;
use Smarty;

final class CacheResourcePluginTest extends TestCase
{
    public function testGetTemplate(): void
    {
        $smarty = $this->getMockBuilder(Smarty::class)->getMock();
        $smarty->oxidcache = new Field('newValue', Field::T_RAW);
        $smarty->security = false;

        $resource = $this->getSmartyExtensionObject();

        $tplSource = 'initialValue';
        $this->assertTrue($resource::getTemplate('templateName', $tplSource, $smarty));
        $this->assertSame('newValue', $tplSource);
        $this->assertFalse($smarty->security);
    }

    public function testGetTemplateIfDemoShopIsActive(): void
    {
        $smarty = $this->getMockBuilder(Smarty::class)->getMock();
        $smarty->oxidcache = new Field('newValue', Field::T_RAW);
        $smarty->security = false;

        $resource = $this->getSmartyExtensionObject(true);

        $tplSource = 'initialValue';
        $this->assertTrue($resource::getTemplate('templateName', $tplSource, $smarty));
        $this->assertSame('newValue', $tplSource);
        $this->assertTrue($smarty->security);
    }

    public function testGetTimestamp(): void
    {
        $smarty = $this->getMockBuilder(Smarty::class)->getMock();

        $resource = $this->getSmartyExtensionObject();

        $time = 2;
        $this->assertTrue($resource::getTimestamp('templateName', $time, $smarty));
        $this->assertIsNumeric($time);
        $this->assertTrue($time > 2);
    }

    public function testGetTimestampIfTimeCacheIsGiven(): void
    {
        $smarty = $this->getMockBuilder(Smarty::class)->getMock();
        $smarty->oxidtimecache = new Field(1, Field::T_RAW);

        $resource = $this->getSmartyExtensionObject();

        $time = 2;
        $this->assertTrue($resource::getTimestamp('templateName', $time, $smarty));
        $this->assertIsNumeric($time);
        $this->assertEquals(1, $time);
    }

    public function testGetSecure(): void
    {
        $smarty = $this->getMockBuilder(Smarty::class)->getMock();
        $resource = $this->getSmartyExtensionObject();
        $this->assertTrue($resource::getSecure('templateName', $smarty));
    }

    public function testGetTrusted(): void
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $resource = new CacheResourcePlugin($smartyContextMock);
        // we just need to test if this method exists
        $this->assertTrue(method_exists($resource, 'getTrusted'));
    }

    private function getSmartyExtensionObject(bool $securityMode = false): CacheResourcePlugin
    {
        $smartyContextMock = $this
        ->getMockBuilder(SmartyContextInterface::class)
        ->getMock();

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        return new CacheResourcePlugin($smartyContextMock);
    }
}
