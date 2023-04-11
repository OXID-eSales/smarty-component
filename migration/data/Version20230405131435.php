<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230405131435 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql($this->getInitialDataContent());
    }

    public function down(Schema $schema): void
    {
    }

    private function getInitialDataContent(): string
    {
        return file_get_contents(__DIR__ . '/initial_data.sql');
    }
}
