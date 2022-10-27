<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Locator;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;
use OxidEsales\Smarty\Module\Template\ModuleTemplatePathResolverInterface;
use Exception;

class AdminTemplateFileLocator implements FileLocatorInterface
{
    public function __construct(
        private Config $context,
        private ModuleTemplatePathResolverInterface $moduleTemplatePathResolver
    ) {}

    public function locate(string $name): string
    {
        $finalTemplatePath = $this->context->getTemplatePath($name, true);

        if (!$finalTemplatePath) {
            try {
                $finalTemplatePath = $this->moduleTemplatePathResolver->resolve($name);
            } catch (Exception $e) {
                $finalTemplatePath = '';
            }
        }

        return $finalTemplatePath;
    }
}
