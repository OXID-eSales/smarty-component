<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Setup\Validator;

interface SmartyPluginDirectoriesValidatorInterface
{
    public function validate(array $directories, string $moduleId, int $shopId): void;
}
