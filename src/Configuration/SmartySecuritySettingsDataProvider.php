<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Configuration;

use OxidEsales\Smarty\Resolver\TemplateDirectoryResolverInterface;

class SmartySecuritySettingsDataProvider implements SmartySecuritySettingsDataProviderInterface
{
    public function __construct(private TemplateDirectoryResolverInterface $directoryResolver)
    {
    }

    /**
     * Define and return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array
    {
        return [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => $this->directoryResolver->getTemplateDirectories(),
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
            ],
        ];
    }
}
