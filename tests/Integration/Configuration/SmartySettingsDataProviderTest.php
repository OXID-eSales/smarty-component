<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Configuration;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\Smarty\Configuration\SmartySettingsDataProvider;
use OxidEsales\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\Smarty\SmartyContextInterface;
use PHPUnit\Framework\TestCase;

class SmartySettingsDataProviderTest extends TestCase
{
    public function testGetSmartySettings()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $dataProvider = new SmartySettingsDataProvider($smartyContextMock, new SmartyDefaultTemplateHandler($smartyContextMock));
        $settings = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'template_dir' => ['testTemplateDir'],
            'compile_id' => '7f96e0d92070fd4733296e5118fd5a01',
            'default_template_handler_func' => [new SmartyDefaultTemplateHandler($smartyContextMock), 'handleTemplate'],
            'debugging' => true,
            'compile_check' => true,
            'php_handling' => 1,
            'security' => false
        ];

        $this->assertEquals($settings, $dataProvider->getSettings());
    }

    private function getSmartyContextMock(): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateEngineDebugMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        $smartyContextMock
            ->method('getTemplateCompileCheckMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplatePhpHandlingMode')
            ->willReturn(1);

        $smartyContextMock
            ->method('getTemplateCompileId')
            ->willReturn('7f96e0d92070fd4733296e5118fd5a01');

        return $smartyContextMock;
    }
}
