<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\Smarty\Module\MetaData\MetaDataDaoInterface;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class TemplateBlockModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    public function __construct(
        private TemplateBlockExtensionDaoInterface $templateBlockExtensionDao,
        private ModulePathResolverInterface $metadataResolver,
        private MetaDataDaoInterface $metaDataDao
    ){}

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($this->templateBlockExtensionDao->exists([$configuration->getId()], $shopId)) {
            return;
        }

        $metadata = $this->metaDataDao->get($this->metadataResolver->getFullModulePathFromConfiguration($configuration->getId(), $shopId));
        $data = $metadata['moduleData'];
        if (isset($data['blocks'])) {
            foreach ($data['blocks'] as $templateBlock) {
                $templateBlockExtension = $this->mapDataToObject($templateBlock);
                $templateBlockExtension->setShopId($shopId);
                $templateBlockExtension->setModuleId($configuration->getId());

                $this->templateBlockExtensionDao->add($templateBlockExtension);
            }
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($this->templateBlockExtensionDao->exists([$configuration->getId()], $shopId)) {
            $this->templateBlockExtensionDao->deleteExtensions($configuration->getId(), $shopId);
        }
    }

    private function mapDataToObject(array $templateBlock): TemplateBlockExtension
    {
        $templateBlockExtension = new TemplateBlockExtension();
        $templateBlockExtension
            ->setName($templateBlock['block'])
            ->setFilePath($templateBlock['file'])
            ->setExtendedBlockTemplatePath($templateBlock['template']);

        if (isset($templateBlock['position'])) {
            $templateBlockExtension->setPosition(
                (int) $templateBlock['position']
            );
        }

        if (isset($templateBlock['theme'])) {
            $templateBlockExtension->setThemeId(
                $templateBlock['theme']
            );
        }

        return $templateBlockExtension;
    }
}
