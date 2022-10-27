<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Template;

interface TemplateDaoInterface
{
    public function add(array $templates, string $moduleId, int $shopId): void;

    public function delete(string $moduleId, int $shopId): void;

    public function getTemplates(int $shopId): array;
}
