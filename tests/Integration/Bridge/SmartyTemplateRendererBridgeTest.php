<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Bridge;

use OxidEsales\Smarty\Bridge\SmartyTemplateRendererBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Smarty;

final class SmartyTemplateRendererBridgeTest extends IntegrationTestCase
{
    public function testSetGetEngine(): void
    {
        $smarty = new Smarty();
        $renderer = $this->get(TemplateRendererInterface::class);
        $bridge = new SmartyTemplateRendererBridge($renderer);
        $bridge->setEngine($smarty);

        $this->assertEquals($smarty, $bridge->getEngine());
    }
}
