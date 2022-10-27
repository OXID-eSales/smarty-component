<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Template;

class ThemedTemplate extends Template
{
    public function __construct(
        string $templateKey,
        string $templatePath,
        private string $templateTheme
    ) {
        parent::__construct($templateKey, $templatePath);
    }

    public function getTemplateTheme(): string
    {
        return $this->templateTheme;
    }
}
