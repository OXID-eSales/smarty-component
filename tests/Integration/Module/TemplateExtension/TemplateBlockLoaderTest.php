<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Smarty\Module\TemplateExtension;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockLoaderInterface;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockNotFoundException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class TemplateBlockLoaderTest extends TestCase
{
    use ContainerTrait;

    public function testGetContent(): void
    {
        $this->installTestModule();

        $loader = $this->get(TemplateBlockLoaderInterface::class);

        $this->assertEquals(
            'test content',
            $loader->getContent('template.tpl', 'testModule')
        );
    }

    public function testGetContentWithNonExistentTemplate(): void
    {
        $this->installTestModule();

        $loader = $this->get(TemplateBlockLoaderInterface::class);

        $this->expectException(TemplateBlockNotFoundException::class);
        $loader->getContent('wrongTemplate.tpl', 'testModule');
    }

    private function installTestModule(): void
    {
        $this->get(ModuleInstallerInterface::class)->install(
            new OxidEshopPackage(__DIR__ . '/Fixtures/testModule')
        );
    }
}
