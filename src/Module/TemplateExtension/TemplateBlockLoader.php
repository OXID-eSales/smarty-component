<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class TemplateBlockLoader implements TemplateBlockLoaderInterface
{
    public function __construct(
        private ContextInterface $context,
        private ModulePathResolverInterface $modulePathResolver,
        private Filesystem $filesystem
    ) {
    }

    public function getContent(string $templatePath, string $moduleId): string
    {
        $fullTemplatePath = $this->getAbsoluteTemplatePath($templatePath, $moduleId);

        if (!$this->filesystem->exists($fullTemplatePath)) {
            throw new TemplateBlockNotFoundException(
                'Template block file ' . $fullTemplatePath . ' for the module with id ' . $moduleId . ' not found.'
            );
        }

        return file_get_contents($fullTemplatePath);
    }

    private function getModulePath(string $moduleId): string
    {
        return $this->modulePathResolver->getFullModulePathFromConfiguration(
            $moduleId,
            $this->context->getCurrentShopId()
        );
    }

    private function getAbsoluteTemplatePath(string $templatePath, string $moduleId): string
    {
        return Path::join(
            $this->getModulePath($moduleId),
            $templatePath
        );
    }
}
