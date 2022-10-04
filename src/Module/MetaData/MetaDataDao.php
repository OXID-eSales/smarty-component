<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProviderInterface;

class MetaDataDao implements MetaDataDaoInterface
{
    private string $metadataFileName = 'metadata.php';

    public function __construct(
        private MetaDataProviderInterface $metadataProvider
    ) {
    }

    public function get(string $modulePath): array
    {
        return $this->metadataProvider->getData($this->getMetadataFilePath($modulePath));
    }

    private function getMetadataFilePath(string $moduleFullPath): string
    {
        return $moduleFullPath . DIRECTORY_SEPARATOR . $this->metadataFileName;
    }
}
