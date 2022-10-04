<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Smarty\Resolver;

interface TemplateDirectoryResolverInterface
{
    public function getTemplateDirectories(): array;
}
