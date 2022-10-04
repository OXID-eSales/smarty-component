<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotReadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException;

class SmartyPluginDirectoriesValidator implements SmartyPluginDirectoriesValidatorInterface
{
    public function __construct(private ModulePathResolverInterface $modulePathResolver)
    {
    }

    public function validate(array $directories, string $moduleId, int $shopId): void
    {
            if ($this->isEmptyArray($directories)) {
                throw new ModuleSettingNotValidException(
                    'Module setting ' .
                     'smartyPluginDirectories' .
                    ' must be not empty'
                );
            }

            $fullPathToModule = $this->modulePathResolver->getFullModulePathFromConfiguration(
                $moduleId,
                $shopId
            );

            foreach ($directories as $directory) {
                $fullPathSmartyPluginDirectory = $fullPathToModule . DIRECTORY_SEPARATOR . $directory;
                if (!is_dir($fullPathSmartyPluginDirectory)) {
                    throw new DirectoryNotExistentException(
                        'Directory ' . $fullPathSmartyPluginDirectory . ' does not exist.'
                    );
                }
                if (!is_readable($fullPathSmartyPluginDirectory)) {
                    throw new DirectoryNotReadableException(
                        'Directory ' . $fullPathSmartyPluginDirectory . ' not readable.'
                    );
                }
            }
    }

    private function isEmptyArray(array $directories): bool
    {
        return count($directories) === 1 && $directories[0] === '';
    }
}
