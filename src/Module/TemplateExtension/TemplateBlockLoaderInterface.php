<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

interface TemplateBlockLoaderInterface
{
    public function getContent(string $templatePath, string $moduleId): string;
}
