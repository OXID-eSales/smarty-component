<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Extension;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;
use OxidEsales\Smarty\Extension\SmartyDefaultTemplateHandler;
use PHPUnit\Framework\TestCase;
use Smarty;

final class SmartyDefaultTemplateHandlerTest extends TestCase
{
    private string $resourceName = 'smartyTemplate.tpl';
    private string $resourceContent = 'The new contents of the file';
    private int $resourceTimeStamp = 1;

    /**
     * If it is not template file or it is not valid,
     * content and timestamp should not be changed.
     *
     * @dataProvider smartyDefaultTemplateHandlerDataProvider
     *
     * @param string $resourceType  The Type of the given file.
     * @param mixed  $givenResource The template to test.
     */
    public function testSmartyDefaultTemplateHandlerWithoutExistingFile(
        string $resourceType,
        string $givenResource
    ): void {
        $resourceName = $this->resourceName;
        $resourceContent = $this->resourceContent;
        $resourceTimestamp = $this->resourceTimeStamp;

        $smarty = new Smarty();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';

        $handler = $this->getSmartyDefaultTemplateHandler($givenResource);
        $return = $handler->handleTemplate(
            $resourceType,
            $resourceName,
            $resourceContent,
            $resourceTimestamp,
            $smarty
        );

        $this->assertFalse($return);
        $this->assertSame($this->resourceContent, $resourceContent);
        $this->assertSame($this->resourceTimeStamp, $resourceTimestamp);
    }

    public function smartyDefaultTemplateHandlerDataProvider(): array
    {
        return [
            ['content', $this->resourceName],
            ['file', $this->resourceName],
            ['file', $this->getTemplateDirectory()]
        ];
    }

    public function testSmartyDefaultTemplateHandler(): void
    {
        $resourceName = $this->resourceName;
        $resourceContent = $this->resourceContent;
        $resourceTimestamp = $this->resourceTimeStamp;

        $smarty = new Smarty();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';

        $template = $this->getTemplateDirectory() . $resourceName;
        $returnContent = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}]' . "\n" . '[{$title}]';

        $handler = $this->getSmartyDefaultTemplateHandler($template);
        $return = $handler->handleTemplate(
            'file',
            $resourceName,
            $resourceContent,
            $resourceTimestamp,
            $smarty
        );

        $this->assertTrue($return);
        $this->assertSame($returnContent, $resourceContent);
        $this->assertSame(filemtime($template), $resourceTimestamp);
    }

    private function getSmartyDefaultTemplateHandler(string $template): SmartyDefaultTemplateHandler
    {
        $locator = $this
        ->getMockBuilder(FileLocatorInterface::class)
        ->getMock();

        $locator
            ->method('locate')
            ->willReturn($template);

        return new SmartyDefaultTemplateHandler($locator);
    }

    private function getTemplateDirectory(): string
    {
        return __DIR__ . '/../Fixtures/';
    }
}
