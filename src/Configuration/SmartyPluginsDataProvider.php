<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Configuration;

class SmartyPluginsDataProvider implements SmartyPluginsDataProviderInterface
{
    public function getPlugins(): array
    {
        return [$this->getShopSmartyPluginDirectory()];
    }

    private function getShopSmartyPluginDirectory(): string
    {
        return __DIR__ . '/../Plugin';
    }
}
