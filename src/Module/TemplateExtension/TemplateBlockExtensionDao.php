<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class TemplateBlockExtensionDao implements TemplateBlockExtensionDaoInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private ShopAdapterInterface $shopAdapter
    ) {
    }

    public function add(TemplateBlockExtension $templateBlockExtension): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxtplblocks')
            ->values([
                'oxid'          => ':id',
                'oxshopid'      => ':shopId',
                'oxmodule'      => ':moduleId',
                'oxtheme'       => ':themeId',
                'oxblockname'   => ':name',
                'oxfile'        => ':filePath',
                'oxtemplate'    => ':templatePath',
                'oxpos'         => ':priority',
                'oxactive'      => '1',
            ])
            ->setParameters([
                'id'            => $this->shopAdapter->generateUniqueId(),
                'shopId'        => $templateBlockExtension->getShopId(),
                'moduleId'      => $templateBlockExtension->getModuleId(),
                'themeId'       => $templateBlockExtension->getThemeId(),
                'name'          => $templateBlockExtension->getName(),
                'filePath'      => $templateBlockExtension->getFilePath(),
                'templatePath'  => $templateBlockExtension->getExtendedBlockTemplatePath(),
                'priority'      => $templateBlockExtension->getPosition(),
            ]);

        $queryBuilder->execute();
    }

    public function getExtensions(string $name, int $shopId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('*')
            ->from('oxtplblocks')
            ->where('oxshopid = :shopId')
            ->andWhere('oxblockname = :name')
            ->andWhere('oxmodule != \'\'')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
            ]);

        $blocksData = $queryBuilder->execute()->fetchAllAssociative();

        return $this->mapDataToObjects($blocksData);
    }

    public function getExtensionsByTemplateName(
        string $templateName,
        array $moduleIds,
        int $shopId,
        array $themeIds = []
    ): array {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('*')
            ->from('oxtplblocks')
            ->where('oxactive = 1')
            ->andWhere('oxshopid = ' . $queryBuilder->createPositionalParameter(
                $shopId,
                ParameterType::INTEGER
                ))
            ->andWhere('oxtemplate = ' . $queryBuilder->createPositionalParameter($templateName))
            ->andWhere('oxmodule in (' . $queryBuilder->createPositionalParameter(
                array_values( $moduleIds),
                Connection::PARAM_STR_ARRAY
                ) . ')')
            ->andWhere('oxtheme in (' . $queryBuilder->createPositionalParameter(
                $this->formActiveThemesId($themeIds),
                Connection::PARAM_STR_ARRAY
                ) . ')');

        $blocksData = $queryBuilder->execute()->fetchAllAssociative();

        return $this->mapDataToObjects($blocksData);
    }

    public function getExtensionsByTheme(int $shopId, array $themeIds = []): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('*')
            ->from('oxtplblocks')
            ->where('oxactive = 1')
            ->andWhere('oxshopid = ' . $queryBuilder->createPositionalParameter(
                    $shopId,
                    ParameterType::INTEGER
                ))
            ->andWhere('oxtheme in (' . $queryBuilder->createPositionalParameter(
                    $this->formActiveThemesId($themeIds),
                    Connection::PARAM_STR_ARRAY
                ) . ')');

        $blocksData = $queryBuilder->execute()->fetchAllAssociative();

        return $this->mapDataToObjects($blocksData);
    }

    public function exists(array $moduleIds, int $shopId): bool
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('oxid')
            ->from('oxtplblocks')
            ->where('oxactive = 1')
            ->andWhere('oxshopid = ' . $queryBuilder->createPositionalParameter(
                    $shopId,
                    ParameterType::INTEGER
                ))
            ->andWhere('oxmodule in (' . $queryBuilder->createPositionalParameter(
                    array_values( $moduleIds),
                    Connection::PARAM_STR_ARRAY
                ) . ')');

        return (bool) $queryBuilder->execute()->fetchOne();
    }

    public function deleteExtensions(string $moduleId, int $shopId): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxtplblocks')
            ->where('oxshopid = :shopId')
            ->andWhere('oxmodule = :moduleId')
            ->setParameters([
                'shopId'    => $shopId,
                'moduleId'  => $moduleId,
            ]);

        $queryBuilder->execute();
    }

    private function mapDataToObjects(array $blocksData): array
    {
        $templateBlockExtensions = [];

        foreach ($blocksData as $blockData) {
            $templateBlock = new TemplateBlockExtension();
            $templateBlock
                ->setShopId(
                    (int) $blockData['OXSHOPID']
                )
                ->setModuleId(
                    $blockData['OXMODULE']
                )
                ->setThemeId(
                    $blockData['OXTHEME']
                )
                ->setName(
                    $blockData['OXBLOCKNAME']
                )
                ->setFilePath(
                    $blockData['OXFILE']
                )
                ->setExtendedBlockTemplatePath(
                    $blockData['OXTEMPLATE']
                )
                ->setPosition(
                    (int) $blockData['OXPOS']
                );

            $templateBlockExtensions[] = $templateBlock;
        }

        return $templateBlockExtensions;
    }

    private function formActiveThemesId(array $activeThemeIds): array
    {
        // if theme is not defined should also be included
        array_unshift($activeThemeIds, '');
        return array_values($activeThemeIds);
    }
}
