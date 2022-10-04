<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Smarty\Resolver;

class TemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    public function __construct(
        private iterable $directoryResolvers
    ) {
    }

    public function getTemplateDirectories(): array
    {
        $directories = [];
        foreach ($this->directoryResolvers as $resolver) {
            $directories[] = $resolver->getTemplateDirectories();
        }

        return array_merge(...$directories);
    }
}
