<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPluginsDataProvider;
use OxidEsales\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class SmartyPluginsDataProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $settings = (new SmartyPluginsDataProvider())->getPlugins();

        $this->assertTrue(file_exists($settings[0] . DIRECTORY_SEPARATOR . 'function.oxcontent.php'));
    }
}
