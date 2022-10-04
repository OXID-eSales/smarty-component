<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Configuration;

use OxidEsales\Smarty\Extension\SmartyTemplateHandlerInterface;
use OxidEsales\Smarty\Resolver\TemplateDirectoryResolverInterface;
use OxidEsales\Smarty\SmartyContextInterface;

class SmartySettingsDataProvider implements SmartySettingsDataProviderInterface
{
    public function __construct(
        private SmartyContextInterface $context,
        private SmartyTemplateHandlerInterface $smartyTemplateHandler,
        private TemplateDirectoryResolverInterface $directoryResolver
    )
    {
    }

    /**
     * Define and return basic smarty settings
     *
     * @return array
     */
    public function getSettings(): array
    {//var_dump($this->directoryResolver->getTemplateDirectories());
        return [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'template_dir' => $this->directoryResolver->getTemplateDirectories(),
            'compile_id' => $this->context->getTemplateCompileId(),
            'default_template_handler_func' => [$this->smartyTemplateHandler, 'handleTemplate'],
            'debugging' => $this->context->getTemplateEngineDebugMode(),
            'compile_check' => $this->context->getTemplateCompileCheckMode(),
            'php_handling' => (int) $this->context->getTemplatePhpHandlingMode(),
            'security' => false
        ];
    }
}
