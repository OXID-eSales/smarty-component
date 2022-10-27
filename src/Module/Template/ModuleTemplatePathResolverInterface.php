<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Template;

interface ModuleTemplatePathResolverInterface
{
    public function resolve(string $templateKey): string;
}
