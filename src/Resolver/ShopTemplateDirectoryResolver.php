<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Resolver;

use OxidEsales\Smarty\SmartyContextInterface;

class ShopTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    public function __construct(
        private SmartyContextInterface $context
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTemplateDirectories(): array
    {
        return $this->context->getTemplateDirectories();
    }
}
