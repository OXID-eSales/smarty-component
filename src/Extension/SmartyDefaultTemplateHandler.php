<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Extension;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;

/**
 * Default Template Handler
 *
 * called when Smarty's file: resource is unable to load a requested file
 */
class SmartyDefaultTemplateHandler implements SmartyTemplateHandlerInterface
{
    public function __construct(
        private FileLocatorInterface $templateFileLocator
    )
    {
    }

    /**
     * Called when a template cannot be obtained from its resource.
     *
     * @param string $resourceType      template type
     * @param string $resourceName      template file name
     * @param string $resourceContent   template file content
     * @param int    $resourceTimestamp template file timestamp
     * @param object $smarty            template processor object (smarty)
     *
     * @return bool
     */
    public function handleTemplate($resourceType, $resourceName, &$resourceContent, &$resourceTimestamp, $smarty): bool
    {
        $fileLoaded = false;
        if ($resourceType === 'file' && !is_readable($resourceName)) {
            $resourceName = $this->templateFileLocator->locate($resourceName);
            $fileLoaded = $this->isFileLoadable($resourceName);
            if ($fileLoaded) {
                $resourceContent = $smarty->_read_file($resourceName);
                $resourceTimestamp = filemtime($resourceName);
            }
        }
        return $fileLoaded;
    }

    /**
     * @param string $resourceName
     *
     * @return bool
     */
    private function isFileLoadable(string $resourceName): bool
    {
        return is_file($resourceName) && is_readable($resourceName);
    }
}
