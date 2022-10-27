<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Template;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

class TemplateDao implements TemplateDaoInterface
{
    private const MODULE_TEMPLATES = 'aModuleTemplates';

    public function __construct(
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
    ) {
    }

    public function add(array $templates, string $moduleId, int $shopId): void
    {
        $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

        $shopSettingValue = array_merge(
            $shopConfigurationSetting->getValue(),
            [
                $moduleId => $templates,
            ]
        );

        $shopConfigurationSetting->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
    }

    public function delete(string $moduleId, int $shopId): void
    {
        $shopConfigurationSetting = $this->getShopConfigurationSetting($shopId);

        $shopSettingValue = $shopConfigurationSetting->getValue();
        unset($shopSettingValue[$moduleId]);

        $shopConfigurationSetting->setValue($shopSettingValue);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
    }

    public function getTemplates(int $shopId): array
    {
        return $this->getShopConfigurationSetting($shopId)->getValue();
    }

    private function getShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                self::MODULE_TEMPLATES,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName(self::MODULE_TEMPLATES)
                ->setType(ShopSettingType::ARRAY)
                ->setValue([]);
        }

        return $shopConfigurationSetting;
    }
}
