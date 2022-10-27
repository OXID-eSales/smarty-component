<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Template;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModuleTemplateDataProvider implements ActiveModuleTemplateDataProviderInterface
{
    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private TemplateDaoInterface $templateDao,
        private ContextInterface $context,
        private ModuleCacheServiceInterface $moduleCacheService
    ) {
    }

    public function getTemplates(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'templates';

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $this->moduleCacheService->put(
                $cacheKey,
                $shopId,
                $this->collectModuleTemplatesForCaching()
            );
        }
        return $this->createTemplatesFromData(
            $this->moduleCacheService->get($cacheKey, $shopId)
        );
    }

    private function collectModuleTemplatesForCaching(): array
    {
        $templates = [];
        $shopId = $this->context->getCurrentShopId();
        $allTemplates = $this->templateDao->getTemplates($shopId);

        foreach ($this->moduleConfigurationDao->getAll($shopId) as $moduleConfiguration) {
            if ($moduleConfiguration->isActivated() && isset($allTemplates[$moduleConfiguration->getId()])) {
                $templates[$moduleConfiguration->getId()] = $allTemplates[$moduleConfiguration->getId()];
            }
        }
        return $templates;
    }

    private function createTemplatesFromData(array $data): array
    {
        $templates = [];
        foreach ($data as $moduleId => $templateData) {
            foreach ($templateData as $templateKey => $templatePath) {
                $templates[$moduleId][] = new Template($templateKey, $templatePath);
            }
        }
        return $templates;
    }
}
