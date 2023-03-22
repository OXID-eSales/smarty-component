<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class SmartyContext implements SmartyContextInterface
{
    public function __construct(
        private ContextInterface $context,
        private Config $config,
        private string $activeAdminTheme
    ) {
    }

    /**
     * @return bool
     */
    public function getTemplateEngineDebugMode(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode === 1 || $debugMode === 3 || $debugMode === 4);
    }

    /**
     * @return bool
     */
    public function showTemplateNames(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode === 8 && !$this->getBackendMode());
    }

    /**
     * @return bool
     */
    public function getTemplateSecurityMode(): bool
    {
        return $this->getDemoShopMode();
    }

    /**
     * @return string
     */
    public function getTemplateCompileDirectory(): string
    {
        return $this->context->getTemplateCacheDirectory();
    }

    /**
     * @return array
     */
    public function getTemplateDirectories(): array
    {
        return [$this->config->getTemplateDir($this->getBackendMode())];
    }

    /**
     * @return string
     */
    public function getTemplateCompileId(): string
    {
        $shopId = $this->context->getCurrentShopId();
        $templateDirectories = $this->getTemplateDirectories();

        return md5(reset($templateDirectories) . '__' . $shopId);
    }

    /**
     * @return bool
     */
    public function getTemplateCompileCheckMode(): bool
    {
        $compileCheck = (bool) $this->getConfigParameter('blCheckTemplates');
        if ($this->config->isProductiveMode()) {
            // override in any case
            $compileCheck = false;
        }
        return $compileCheck;
    }

    /**
     * @return int
     */
    public function getTemplatePhpHandlingMode(): int
    {
        return (int) $this->getConfigParameter('iSmartyPhpHandling');
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName): string
    {
        return (string) $this->config->getTemplatePath($templateName, $this->getBackendMode());
    }

    /**
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->context->getSourcePath();
    }

    public function isSmartyForContentDeactivated(): bool
    {
        return (bool) $this->getConfigParameter('deactivateSmartyForCmsContent');
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }

    /**
     * @return bool
     */
    private function getBackendMode(): bool
    {
        return $this->config->isAdmin();
    }

    /**
     * @return bool
     */
    private function getDemoShopMode(): bool
    {
        return (bool)$this->config->isDemoShop();
    }

    public function getActiveThemeId(): string
    {
        return $this->getBackendMode()
            ? $this->activeAdminTheme
            : $this->getActiveFrontendThemeId();
    }

    private function getActiveFrontendThemeId(): string
    {
        return $this->config->getConfigParam('sCustomTheme')
            ?: $this->config->getConfigParam('sTheme')
            ?: '';
    }
}
