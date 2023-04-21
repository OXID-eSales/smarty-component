<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Loader;

use org\bovigo\vfs\vfsStream;
use OxidEsales\Smarty\Loader\TemplateLoader;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use PHPUnit\Framework\TestCase;

final class TemplateLoaderTest extends TestCase
{
    public function testGetContext(): void
    {
        $name = 'testSmartyTemplate.tpl';
        $context = 'The new contents of the file';
        $templateDir = vfsStream::setup('testTemplateDir');
        $template = vfsStream::newFile($name)
            ->at($templateDir)
            ->setContent($context)
            ->url();

        $locator = $this->getFileLocatorMock($template);
        $nameResolver = $this->getTemplateFileResolverMock($name);
        $loader = new TemplateLoader($locator, $nameResolver);

        $this->assertSame($context, $loader->getContext($template));
    }

    private function getFileLocatorMock(string $path): FileLocatorInterface
    {
        $locator = $this
            ->getMockBuilder(FileLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $locator->method('locate')
            ->willReturn($path);

        return $locator;
    }

    private function getTemplateFileResolverMock(string $name): TemplateFileResolverInterface
    {
        $locator = $this
            ->getMockBuilder(TemplateFileResolverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $locator->method('getFilename')
            ->willReturn($name);

        return $locator;
    }
}
