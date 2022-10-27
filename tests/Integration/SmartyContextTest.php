<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Smarty\SmartyContext;
use PHPUnit\Framework\TestCase;

class SmartyContextTest extends TestCase
{
    public function getTemplateEngineDebugModeDataProvider(): array
    {
        return [
            [1, true],
            [3, true],
            [4, true],
            [6, false],
            ['two', false],
            ['5', false]
        ];
    }

    /**
     * @dataProvider getTemplateEngineDebugModeDataProvider
     */
    public function testGetTemplateEngineDebugMode(mixed $configValue, bool $debugMode): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('iDebug')
            ->willReturn($configValue);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame($debugMode, $smartyContext->getTemplateEngineDebugMode());
    }

    public function showTemplateNamesDataProvider(): array
    {
        return [
            [8, false, true],
            [8, true, false],
            [5, false, false],
            [5, false, false],
        ];
    }

    /**
     * @dataProvider showTemplateNamesDataProvider
     */
    public function testShowTemplateNames(int $configValue, bool $adminMode, bool $result): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('iDebug')
            ->willReturn($configValue);
        $config->method('isAdmin')
            ->willReturn($adminMode);


        Registry::getConfig()->setConfigParam('iDebug', $configValue);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame($result, $smartyContext->showTemplateNames());
    }

    public function testGetTemplateSecurityMode(): void
    {
        $config = $this->getConfigMock();
        $config->method('isDemoShop')
            ->willReturn(true);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame(true, $smartyContext->getTemplateSecurityMode());
    }

    public function testGetTemplateCompileCheckMode(): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('blCheckTemplates')
            ->willReturn(true);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame(true, $smartyContext->getTemplateCompileCheckMode());
    }

    public function testGetTemplateCompileCheckModeInProductiveMode(): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('blCheckTemplates')
            ->willReturn(true);
        $config->method('isProductiveMode')
            ->willReturn(true);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertFalse($smartyContext->getTemplateCompileCheckMode());
    }

    public function testGetTemplatePhpHandlingMode(): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('iSmartyPhpHandling')
            ->willReturn(1);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame(1, $smartyContext->getTemplatePhpHandlingMode());
    }

    public function testGetTemplatePath(): void
    {
        $config = $this->getConfigMock();
        $config->method('isAdmin')
            ->willReturn(false);
        $config->method('getTemplatePath')
            ->with('testTemplate', false)
            ->willReturn('templatePath');

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame('templatePath', $smartyContext->getTemplatePath('testTemplate'));
    }

    public function testGetTemplateCompileDirectory(): void
    {
        $context = new ContextStub();
        $context->setTemplateCacheDirectory('testCompileDir');
        $config = $this->getConfigMock();

        $smartyContext = new SmartyContext($context, $config, 'admin_smarty');
        $this->assertSame('testCompileDir', $smartyContext->getTemplateCompileDirectory());
    }

    public function testGetTemplateDirectories(): void
    {
        $config = $this->getConfigMock();
        $config->method('getTemplateDir')
            ->willReturn('testTemplateDir');
        $config->method('isAdmin')
            ->willReturn(false);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertSame(['testTemplateDir'], $smartyContext->getTemplateDirectories());
    }

    public function testGetTemplateCompileId(): void
    {
        $templateDir = 'testCompileDir';
        $shopId = 1;
        $context = new ContextStub();
        $context->setCurrentShopId($shopId);
        $config = $this->getConfigMock();
        $config->method('getTemplateDir')
            ->willReturn($templateDir);
        $config->method('isAdmin')
            ->willReturn(false);

        $smartyContext = new SmartyContext($context, $config, 'admin_smarty');
        $this->assertSame(md5($templateDir . '__' . $shopId), $smartyContext->getTemplateCompileId());
    }

    public function testGetSourcePath(): void
    {
        $config = $this->getConfigMock();
        $context = new ContextStub();
        $context->setSourcePath('testSourcePath');

        $smartyContext = new SmartyContext($context, $config, 'admin_smarty');
        $this->assertSame('testSourcePath', $smartyContext->getSourcePath());
    }

    public function testIsSmartyForContentDeactivated(): void
    {
        $config = $this->getConfigMock();
        $config->method('getConfigParam')
            ->with('deactivateSmartyForCmsContent')
            ->willReturn(1);

        $smartyContext = new SmartyContext(new ContextStub(), $config, 'admin_smarty');
        $this->assertTrue($smartyContext->isSmartyForContentDeactivated());
    }

    /**
     * @return Config
     */
    private function getConfigMock()
    {
        $configMock = $this
            ->getMockBuilder(Config::class)
            ->getMock();

        return $configMock;
    }

}
