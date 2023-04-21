<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\Smarty\Configuration\SmartySecuritySettingsDataProvider;
use OxidEsales\Smarty\Resolver\TemplateDirectoryResolverInterface;
use PHPUnit\Framework\TestCase;
use Smarty;

final class SmartySecuritySettingsDataProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        new Smarty();
    }

    public function testGetSecuritySettings(): void
    {
        $dataProvider = new SmartySecuritySettingsDataProvider($this->getTemplateDirectoryResolverMock());
        $settings = [
            'php_handling' => 2,
            'security' => true,
            'secure_dir' => ['testTemplateDir'],
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
            ]
        ];

        $this->assertEquals($settings, $dataProvider->getSecuritySettings());
    }

    private function getTemplateDirectoryResolverMock(): TemplateDirectoryResolverInterface
    {
        $mock = $this
            ->getMockBuilder(TemplateDirectoryResolverInterface::class)
            ->getMock();

        $mock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        return $mock;
    }
}
