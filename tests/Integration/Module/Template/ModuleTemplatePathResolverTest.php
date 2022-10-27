<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\Template;

use OxidEsales\Smarty\Module\Template\ActiveModuleTemplateDataProviderInterface;
use OxidEsales\Smarty\Module\Template\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\Smarty\Module\Template\ModuleTemplateKeyNotFoundException;
use OxidEsales\Smarty\Module\Template\ModuleTemplateNotFoundException;
use OxidEsales\Smarty\Module\Template\ModuleTemplatePathResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Filesystem\Filesystem;

final class ModuleTemplatePathResolverTest extends TestCase
{
    use ProphecyTrait;

    public function testWithExistentTemplate(): void
    {
        $expectedPath = 'path1/templatePath2';

        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->exists($expectedPath)->willReturn(true);

        $resolver = new ModuleTemplatePathResolver(
            $this->getActiveModulesDataProviderMock(),
            $filesystem->reveal()
        );

        $this->assertEquals(
            $expectedPath,
            $resolver->resolve('module1key2')
        );
    }

    public function testWithNotExistentTemplate(): void
    {
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->exists('path1/templatePath2')->willReturn(false);

        $resolver = new ModuleTemplatePathResolver(
            $this->getActiveModulesDataProviderMock(),
            $filesystem->reveal()
        );

        $this->expectException(ModuleTemplateNotFoundException::class);

        $resolver->resolve('module1key2');


    }

    public function testWithNotExistentTemplateKey(): void
    {
        $resolver = new ModuleTemplatePathResolver(
            $this->getActiveModulesDataProviderMock(),
            $this->getActiveModuleTemplateDataProviderMock(),
            $this->prophesize(Filesystem::class)->reveal()
        );

        $this->expectException(ModuleTemplateKeyNotFoundException::class);

        $resolver->resolve('nonExistentKey');
    }

    private function getActiveModuleTemplateDataProviderMock(): ActiveModuleTemplateDataProviderInterface
    {
        $activeModulesDataProvider = $this->prophesize(ActiveModuleTemplateDataProviderInterface::class);
        $activeModulesDataProvider->getTemplates()->willReturn([
            'module1' => [
                new Template('module1key1', 'templatePath'),
                new Template('module1key2', 'templatePath2'),
            ],
            'module2' => [
                new Template('module2key1', 'templatePath')
            ],
        ]);

        return $activeModulesDataProvider->reveal();
    }

    private function getActiveModulesDataProviderMock(): ActiveModulesDataProviderInterface
    {
        $activeModulesDataProvider = $this->prophesize(ActiveModulesDataProviderInterface::class);

        $activeModulesDataProvider->getModulePaths()->willReturn([
            'module1' => 'path1',
            'module2' => 'path2'
        ]);
        return $activeModulesDataProvider->reveal();
    }
}
