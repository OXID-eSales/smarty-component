<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Plugin;

interface SmartyPluginDaoInterface
{
    public function add(array $smartyPluginDirectories, string $moduleId, int $shopId): void;

    public function delete(string $moduleId, int $shopId): void;

    public function getPluginDirectories(int $shopId): array;
}
