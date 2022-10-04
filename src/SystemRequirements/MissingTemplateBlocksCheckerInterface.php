<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\SystemRequirements;

interface MissingTemplateBlocksCheckerInterface
{
    public function collectMissingTemplateBlockExtensions(): array;
}
