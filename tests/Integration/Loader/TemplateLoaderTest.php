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

class TemplateLoaderTest extends TestCase
{
    public function testGetContext(): void
    {
        $name = 'testSmartyTemplate.tpl';
        $context = "The new contents of the file";
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

    /**
     * @param $path
     *
     * @return FileLocatorInterface
     */
    private function getFileLocatorMock($path): FileLocatorInterface
    {
        $locator = $this
            ->getMockBuilder('OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $locator->method('locate')
            ->willReturn($path);

        return $locator;
    }

    /**
     * @param $path
     *
     * @return TemplateFileResolverInterface
     */
    private function getTemplateFileResolverMock($name): TemplateFileResolverInterface
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
