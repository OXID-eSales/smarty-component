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
use OxidEsales\Smarty\Module\Plugin\SmartyPluginDaoInterface;
use OxidEsales\Smarty\Module\Setup\Validator\SmartyPluginDirectoriesValidatorInterface;

class ShopConfigurationSmartyPluginDirectoryHandler implements ModuleConfigurationHandlerInterface
{
    public function __construct(private SmartyPluginDaoInterface $smartyPluginDao,
                                private ModulePathResolverInterface $metadataResolver,
                                private MetaDataDaoInterface $metaDataDao,
                                private SmartyPluginDirectoriesValidatorInterface $directoriesValidator
    ) {}

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        $metadata = $this->metaDataDao->get($this->metadataResolver->getFullModulePathFromConfiguration($configuration->getId(), $shopId));
        $data = $metadata['moduleData'];
        if (isset($data['smartyPluginDirectories']) && is_array($data['smartyPluginDirectories'])) {
            $this->directoriesValidator->validate($data['smartyPluginDirectories'], $configuration->getId(), $shopId);
            $this->smartyPluginDao->add($data['smartyPluginDirectories'], $configuration->getId(), $shopId);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        $this->smartyPluginDao->delete($configuration->getId(), $shopId);
    }
}
