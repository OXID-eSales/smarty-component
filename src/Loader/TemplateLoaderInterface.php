<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Loader;

use OxidEsales\Smarty\Exception\TemplateFileNotFoundException;

/**
 * Interface TemplateLoaderInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader
 */
interface TemplateLoaderInterface
{
    /**
     * Returns the content of the given template.
     *
     * @param string $name The name of the template
     *
     * @return string
     */
    public function getContext($name): string;

    /**
     * @throws TemplateFileNotFoundException
     */
    public function findTemplate($name): string;
}
