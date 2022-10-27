<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Bridge;

use OxidEsales\Smarty\Bridge\SmartyTemplateRendererBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class SmartyTemplateRendererBridgeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTemplateRenderer()
    {
        $renderer = $this
            ->getMockBuilder(TemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bridge = new SmartyTemplateRendererBridge($renderer);

        $this->assertSame($renderer, $bridge->getTemplateRenderer());
    }
}
