<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\TemplateExtension;

use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * @internal
 */
final class TemplateBlockDaoTest extends IntegrationTestCase
{
    use ContainerTrait;

    private TemplateBlockExtensionDaoInterface $templateBlockDao;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareTestData();
    }

    public function testAddTemplateBlock(): void
    {
        $templateBlock = new TemplateBlockExtension();
        $templateBlock
            ->setName('testAddTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setShopId(1)
            ->setThemeId('testThemeId');

        $templateBlockDao = $this->getTemplateBlockDao();
        $templateBlockDao->add($templateBlock);

        $this->assertEquals(
            [$templateBlock],
            $templateBlockDao->getExtensions('testAddTemplateBlock', 1)
        );
    }

    public function testDeleteAllModuleTemplateBlocks(): void
    {
        $this->templateBlockDao->deleteExtensions('testModuleId', 1);
        $this->templateBlockDao->deleteExtensions('testModuleId2', 1);
        $this->templateBlockDao->deleteExtensions('testModuleId3', 1);

        $this->assertEquals(
            [],
            $this->templateBlockDao->getExtensions('testTemplateBlock', 1)
        );
    }

    public function testGetExtensionsByTemplateNameOnlyModuleGiven(): void
    {
        $blocks = $this->templateBlockDao
            ->getExtensionsByTemplateName('shopTemplatePath', ['testModuleId', 'testModuleId2'], 1);

        $this->assertCount(1, $blocks);
    }

    public function testGetExtensionsByTemplateNameModuleAndThemeGiven(): void
    {
        $blocks = $this->templateBlockDao
            ->getExtensionsByTemplateName(
                'shopTemplatePath',
                ['testModuleId', 'testModuleId2'],
                1,
                ['testThemeId', 'testThemeId2']
            );

        $this->assertCount(3, $blocks);
    }

    public function testGetExtensionsByTheme(): void
    {
        $blocks = $this->templateBlockDao->getExtensionsByTheme(1, ['testThemeId', 'testThemeId2']);

        $this->assertCount(4, $blocks);
    }

    public function testGetExtensionsByThemeNonGiven(): void
    {
        $blocks = $this->templateBlockDao->getExtensionsByTheme(1);

        $this->assertCount(1, $blocks);
    }

    public function testExistExtension(): void
    {
        $this->assertTrue($this->templateBlockDao->exists(['testModuleId', 'testModuleId2'], 1));
    }

    public function testDoesNotExistExtension(): void
    {
        $this->assertFalse($this->templateBlockDao->exists(['testModuleDoesNotExist'], 1));
    }

    private function getTemplateBlockDao(): TemplateBlockExtensionDaoInterface
    {
        return $this->get(TemplateBlockExtensionDaoInterface::class);
    }

    private function prepareTestData(): void
    {
        $templateBlock = new TemplateBlockExtension();
        $templateBlock
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setThemeId('testThemeId')
            ->setShopId(1);

        $templateBlock2 = new TemplateBlockExtension();
        $templateBlock2
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(2)
            ->setModuleId('testModuleId2')
            ->setThemeId('testThemeId2')
            ->setShopId(1);

        $templateBlock3 = new TemplateBlockExtension();
        $templateBlock3
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(2)
            ->setModuleId('testModuleId3')
            ->setThemeId('testThemeId2')
            ->setShopId(1);

        $templateBlockWithoutTheme = new TemplateBlockExtension();
        $templateBlockWithoutTheme
            ->setName('testTemplateBlock')
            ->setFilePath('blockFilePath')
            ->setExtendedBlockTemplatePath('shopTemplatePath')
            ->setPosition(1)
            ->setModuleId('testModuleId')
            ->setShopId(1);

        $this->templateBlockDao = $this->getTemplateBlockDao();
        $this->templateBlockDao->add($templateBlock);
        $this->templateBlockDao->add($templateBlock2);
        $this->templateBlockDao->add($templateBlock3);
        $this->templateBlockDao->add($templateBlockWithoutTheme);
    }
}
