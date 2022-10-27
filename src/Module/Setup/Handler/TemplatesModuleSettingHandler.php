<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\Smarty\Module\MetaData\MetaDataDaoInterface;
use OxidEsales\Smarty\Module\Template\TemplateDaoInterface;

class TemplatesModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    public function __construct(
        private TemplateDaoInterface $templateDao,
        private ModulePathResolverInterface $metadataResolver,
        private MetaDataDaoInterface $metaDataDao,
    ) {
    }

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        $metadata = $this->metaDataDao->get($this->metadataResolver->getFullModulePathFromConfiguration($configuration->getId(), $shopId));
        $data = $metadata['moduleData'];
        if (isset($data['templates'])) {
            $this->templateDao->add($data['templates'], $configuration->getId(), $shopId);
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        $this->templateDao->delete($configuration->getId(), $shopId);
    }
}
