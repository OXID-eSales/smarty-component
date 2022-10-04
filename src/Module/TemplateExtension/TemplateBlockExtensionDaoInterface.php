<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

interface TemplateBlockExtensionDaoInterface
{
    public function add(TemplateBlockExtension $templateBlockExtension): void;

    public function getExtensions(string $name, int $shopId): array;

    public function getExtensionsByTemplateName(
        string $templateName, array $moduleIds, int $shopId, array $themeIds = []
    ): array;

    public function getExtensionsByTheme(int $shopId, array $themeIds = []): array;

    public function exists(array $moduleIds, int $shopId): bool;

    public function deleteExtensions(string $moduleId, int $shopId): void;
}
