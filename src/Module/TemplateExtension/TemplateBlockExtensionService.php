<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Log\LoggerInterface;

class TemplateBlockExtensionService implements TemplateBlockExtensionServiceInterface
{
    private bool $tplBlocksExist;

    public function __construct(
        private ContextInterface $context,
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private TemplateBlockExtensionDaoInterface $blockExtensionDao,
        private TemplateBlockLoaderInterface $blockLoader,
        private LoggerInterface $logger,
        private ShopAdapterInterface $shopAdapter
    ) {
    }

    public function getTemplateBlockExtensions(string $templateFileName): array
    {
        $templateBlocksWithContent = [];

        if ($this->isShopTemplateBlockOverriddenByActiveModule()) {
            $shopId = $this->context->getCurrentShopId();

            $activeModulesIds = $this->activeModulesDataProvider->getModuleIds();
            $activeThemeIds = $this->shopAdapter->getActiveThemesList();

            $activeBlockTemplates = $this->blockExtensionDao->getExtensionsByTemplateName($templateFileName, $activeModulesIds, $shopId, $activeThemeIds);

            if ($activeBlockTemplates) {
                $activeBlockTemplatesByTheme = $this->filterTemplateBlocks($activeBlockTemplates);
                $templateBlocksWithContent = $this->fillTemplateBlockWithContent($activeBlockTemplatesByTheme);
            }
        }

        return $templateBlocksWithContent;
    }

    /**
     * Check if at least one active module overrides at least one template (in active shop).
     * To win performance when:
     * - no active modules exists.
     * - none active module overrides template.
     */
    private function isShopTemplateBlockOverriddenByActiveModule(): bool
    {
        if (isset($this->tplBlocksExist)) {
            return $this->tplBlocksExist;
        }

        $moduleOverridesTemplate = false;

        $activeModulesIds = $this->activeModulesDataProvider->getModuleIds();
        if (count($activeModulesIds)) {
            $moduleOverridesTemplate = $this->blockExtensionDao->exists($activeModulesIds, $this->context->getCurrentShopId());
        }
        $this->tplBlocksExist = $moduleOverridesTemplate;

        return $moduleOverridesTemplate;
    }

    /**
     * Leave only one element for items grouped by fields: OXTEMPLATE and OXBLOCKNAME
     *
     * Pick only one element from each group if OXTHEME contains (by following priority):
     * - Active theme id
     * - Parent theme id of active theme
     * - Undefined
     *
     * Example of $activeBlockTemplates:
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_a" (group a)
     *  OXTHEME = ""
     *  "content_a_default"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_a" (group a)
     *  OXTHEME = "parent_of_active_theme"
     *  "content_a_parent"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_a" (group a)
     *  OXTHEME = "active_theme"
     *  "content_a_active"
     *
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_b" (group b)
     *  OXTHEME = ""
     *  "content_b_default"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_b" (group b)
     *  OXTHEME = "parent_of_active_theme"
     *  "content_b_parent"
     *
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_c" (group c)
     *  OXTHEME = ""
     *  OXFILE = "x"
     *  "content_c_x_default"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_c" (group c)
     *  OXTHEME = ""
     *  OXFILE = "y"
     *  "content_c_y_default"
     *
     * Example of return:
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_a" (group a)
     *  OXTHEME = "active_theme"
     *  "content_a_active"
     *
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_b" (group b)
     *  OXTHEME = "parent_of_active_theme"
     *  "content_b_parent"
     *
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_c" (group c)
     *  OXTHEME = ""
     *  OXFILE = "x"
     *  "content_c_x_default"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_c" (group c)
     *  OXTHEME = ""
     *  OXFILE = "y"
     *  "content_c_y_default"
     */
    private function filterTemplateBlocks(array $activeBlockTemplates): array
    {
        $templateBlocks = $activeBlockTemplates;

        $templateBlocksToExchange = $this->formListOfDuplicatedBlocks($activeBlockTemplates);

        if ($templateBlocksToExchange['theme']) {
            $templateBlocks = $this->removeDefaultBlocks($activeBlockTemplates, $templateBlocksToExchange);
        }

        if ($templateBlocksToExchange['custom_theme']) {
            $templateBlocks = $this->removeParentBlocks($templateBlocks, $templateBlocksToExchange);
        }

        return $templateBlocks;
    }

    /**
     * Form list of blocks which has duplicates for specific theme.
     */
    private function formListOfDuplicatedBlocks(array $activeBlockTemplates): array
    {
        $templateBlocksToExchange = [];
        $customThemeId = $this->shopAdapter->getCustomTheme();

        /** @var TemplateBlockExtension $activeBlockTemplate */
        foreach ($activeBlockTemplates as $activeBlockTemplate) {
            if ($activeBlockTemplate->getThemeId()) {
                if ($customThemeId && $customThemeId === $activeBlockTemplate->getThemeId()) {
                    $templateBlocksToExchange['custom_theme'][] = $this->prepareBlockKey($activeBlockTemplate);
                } else {
                    $templateBlocksToExchange['theme'][] = $this->prepareBlockKey($activeBlockTemplate);
                }
            }
        }

        return $templateBlocksToExchange;
    }

    /**
     * Remove default blocks whose have duplicate for specific theme.
     */
    private function removeDefaultBlocks(array $activeBlockTemplates, array $templateBlocksToExchange): array
    {
        $templateBlocks = [];
        /** @var TemplateBlockExtension $activeBlockTemplate */
        foreach ($activeBlockTemplates as $activeBlockTemplate) {
            if (
                !in_array($this->prepareBlockKey($activeBlockTemplate), $templateBlocksToExchange['theme'])
                || $activeBlockTemplate->getThemeId()
            ) {
                $templateBlocks[] = $activeBlockTemplate;
            }
        }

        return $templateBlocks;
    }

    /**
     * Remove parent theme blocks whose have duplicate for custom theme.
     */
    private function removeParentBlocks(array $templateBlocks, array $templateBlocksToExchange): array
    {
        $activeBlockTemplates = $templateBlocks;
        $templateBlocks = [];
        $customThemeId = $this->shopAdapter->getCustomTheme();
        /** @var TemplateBlockExtension $activeBlockTemplate */
        foreach ($activeBlockTemplates as $activeBlockTemplate) {
            if (
                !in_array($this->prepareBlockKey($activeBlockTemplate), $templateBlocksToExchange['custom_theme'])
                || $activeBlockTemplate->getThemeId() === $customThemeId
            ) {
                $templateBlocks[] = $activeBlockTemplate;
            }
        }

        return $templateBlocks;
    }

    /**
     * Fill array with template content or skip if template does not exist.
     * Logs error message if template does not exist.
     *
     * Example of $activeBlockTemplates:
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_a"
     *  "content_a_active"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_b"
     *  OXFILE = "x"
     *  "content_b_x_default"
     *
     *  OXTEMPLATE = "requested_template_name"  OXBLOCKNAME = "block_name_b"
     *  OXFILE = "y"
     *  "content_b_y_default"
     *
     * Example of return:
     *
     * $templateBlocks = [
     *   block_name_a = [
     *     0 => "content_a_active"
     *   ],
     *   block_name_c = [
     *     0 => "content_b_x_default",
     *     1 => "content_b_y_default"
     *   ]
     * ]
     */
    private function fillTemplateBlockWithContent(array $blockTemplates): array
    {
        $templateBlocksWithContent = [];

        /** @var TemplateBlockExtension $activeBlockTemplate */
        foreach ($blockTemplates as $activeBlockTemplate) {
            try {
                if (!is_array($templateBlocksWithContent[$activeBlockTemplate->getName()])) {
                    $templateBlocksWithContent[$activeBlockTemplate->getName()] = [];
                }
                $templateBlocksWithContent[$activeBlockTemplate->getName()][] = $this
                    ->blockLoader
                    ->getContent(
                        $activeBlockTemplate->getFilePath(),
                        $activeBlockTemplate->getModuleId()
                    );
            } catch (TemplateBlockNotFoundException $exception) {
                $this->logger->error($exception->getMessage(), [$exception]);
            }
        }

        return $templateBlocksWithContent;
    }

    /**
     * Prepare indicator for template block.
     * This indicator might be used to identify same template block for different theme.
     */
    private function prepareBlockKey(TemplateBlockExtension $activeBlockTemplate): string
    {
        return $activeBlockTemplate->getExtendedBlockTemplatePath() . $activeBlockTemplate->getName();
    }
}
