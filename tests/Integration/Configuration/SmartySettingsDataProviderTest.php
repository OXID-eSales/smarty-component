<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Configuration;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\Smarty\Configuration\SmartySettingsDataProvider;
use OxidEsales\Smarty\Configuration\SmartySettingsDataProviderInterface;
use OxidEsales\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\Smarty\Extension\SmartyTemplateHandlerInterface;
use OxidEsales\Smarty\Resolver\TemplateDirectoryResolverInterface;
use OxidEsales\Smarty\SmartyContextInterface;
use PHPUnit\Framework\TestCase;

class SmartySettingsDataProviderTest extends TestCase
{
    public function testGetSmartySettings()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $templateResolver = $this->getTemplateDirectoryResolverMock();

        $templateHandler = $this->getSmartyTemplateHandlerMock();

        $dataProvider = new SmartySettingsDataProvider(
            $smartyContextMock,
            $templateHandler,
            $templateResolver
        );
        $settings = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'template_dir' => ['testTemplateDir'],
            'compile_id' => '7f96e0d92070fd4733296e5118fd5a01',
            'default_template_handler_func' => [$templateHandler, 'handleTemplate'],
            'debugging' => true,
            'compile_check' => true,
            'php_handling' => 1,
            'security' => false
        ];

        $this->assertEquals($settings, $dataProvider->getSettings());
    }

    private function getTemplateDirectoryResolverMock(): TemplateDirectoryResolverInterface
    {
        $resolverMock = $this
            ->getMockBuilder(TemplateDirectoryResolverInterface::class)
            ->getMock();

        $resolverMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        return $resolverMock;
    }

    private function getSmartyTemplateHandlerMock(): SmartyTemplateHandlerInterface
    {
        return $this
            ->getMockBuilder(SmartyTemplateHandlerInterface::class)
            ->getMock();
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
