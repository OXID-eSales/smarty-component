<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Loader;

use OxidEsales\Smarty\Exception\TemplateFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;

class TemplateLoader implements TemplateLoaderInterface
{
    public function __construct(
        private FileLocatorInterface $fileLocator,
        private TemplateFileResolverInterface $templateFileResolver
    ) {
    }

    /**
     * Returns the content of the given template.
     *
     * @param string $name The name of the template
     *
     * @return string
     *
     * @throws TemplateFileNotFoundException
     */
    public function getContext($name): string
    {
        $path = $this->findTemplate($name);

        return file_get_contents($path);
    }

    /**
     * @param string $name A template name
     *
     * @return string
     *
     * @throws TemplateFileNotFoundException
     */
    public function findTemplate($name): string
    {
        $filename = $this->templateFileResolver->getFilename($name);
        $file = $this->fileLocator->locate($filename);

        if (false === $file || null === $file || '' === $file) {
            throw new TemplateFileNotFoundException(sprintf('Template "%s" not found', $name));
        }
        return $file;
    }
}
