<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Bridge;

use OxidEsales\Smarty\Bridge\SmartyTemplateRendererBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;

final class SmartyTemplateRendererBridgeTest extends TestCase
{
    public function testGetTemplateRenderer(): void
    {
        $renderer = $this
            ->getMockBuilder(TemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bridge = new SmartyTemplateRendererBridge($renderer);

        $this->assertSame($renderer, $bridge->getTemplateRenderer());
    }
}
