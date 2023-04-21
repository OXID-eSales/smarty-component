<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Unit\Configuration;

use OxidEsales\Smarty\Configuration\SmartyPluginsDataProvider;
use PHPUnit\Framework\TestCase;

final class SmartyPluginsDataProviderTest extends TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff(): void
    {
        $settings = (new SmartyPluginsDataProvider())->getPlugins();

        $this->assertFileExists($settings[0] . DIRECTORY_SEPARATOR . 'function.oxcontent.php');
    }
}
