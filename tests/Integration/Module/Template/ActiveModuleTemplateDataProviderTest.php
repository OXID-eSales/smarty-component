<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration\Module\Template;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Smarty\Module\Template\ActiveModuleTemplateDataProvider;
use OxidEsales\Smarty\Module\Template\Template;
use OxidEsales\Smarty\Module\Template\TemplateDaoInterface;
use PHPUnit\Framework\TestCase;

final class ActiveModuleTemplateDataProviderTest extends TestCase
{
    use ContainerTrait;

    private $activeModuleId = 'activeModuleId';
    private $activeModulePath = 'some-path-active';
    private $activeModuleSource = 'some-source-active';
    private $inactiveModuleId = 'inActiveModuleId';
    private $inactiveModulePath = 'some-path-inactive';
    private $inactiveModuleSource = 'some-source-inactive';
    private $moduleConfiguration;

    private BasicContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new BasicContext();
        $this->prepareTestShopConfiguration();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testGetTemplates(): void
    {
        $dataProvider = new ActiveModuleTemplateDataProvider(
            $this->moduleConfiguration,
            $this->get(TemplateDaoInterface::class),
            $this->get(ContextInterface::class),
            $this->getDummyCache()
        );
        $this->assertEquals(
            [
                $this->activeModuleId => [
                    new Template('activeTemplate', 'activeTemplatePath'),
                ]
            ],
            $dataProvider->getTemplates()
        );
    }

    public function testGetTemplatesUsesCacheIfItExists(): void
    {
        $templatePathInCache = uniqid('some-path', true);
        $cachedData = ['some-module' => ['some-template.tpl' => $templatePathInCache]];
        $cache = $this->getDummyCache();
        $cache->put('templates', 1, $cachedData);

        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($cache);
        $templatePath = $activeModulesDataProvider->getTemplates()['some-module'][0]->getTemplatePath();

        $this->assertEquals($templatePathInCache, $templatePath);
    }

    public function testGetTemplatesUsesCacheIfItDoesNotExist(): void
    {
        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($this->getDummyCache());

        $this->assertEquals(
            [
                $this->activeModuleId => [
                    new Template('activeTemplate', 'activeTemplatePath'),
                ]
            ],
            $activeModulesDataProvider->getTemplates()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setActivated(true)
            ->setModuleSource($this->activeModuleSource);

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setActivated(false)
            ->setModuleSource($this->inactiveModuleSource);

        $templateDao = $this->get(TemplateDaoInterface::class);
        $templateDao->add(['activeTemplate' => 'activeTemplatePath'], $this->activeModuleId, $this->context->getDefaultShopId());
        $templateDao->add(['inactiveTemplate' => 'inactiveTemplatePath'], $this->inactiveModuleId, $this->context->getDefaultShopId());

        $this->moduleConfiguration = $this->get(ModuleConfigurationDaoInterface::class);
        $this->moduleConfiguration->save($activeModule, $this->context->getDefaultShopId());
        $this->moduleConfiguration->save($inactiveModule, $this->context->getDefaultShopId());
    }

    private function cleanUpTestData(): void
    {
        $this->get(ModuleActivationServiceInterface::class)->deactivate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function getActiveModulesDataProviderWithCache(ModuleCacheServiceInterface $cache): ActiveModuleTemplateDataProvider
    {
        return new ActiveModuleTemplateDataProvider(
            $this->get(ModuleConfigurationDaoInterface::class),
            $this->get(TemplateDaoInterface::class),
            $this->get(ContextInterface::class),
            $cache
        );
    }

    private function getDummyCache(): ModuleCacheServiceInterface
    {
        return new class implements ModuleCacheServiceInterface {
            private $cache;

            public function invalidate(string $moduleId, int $shopId): void
            {
            }

            public function put(string $key, int $shopId, array $data): void
            {
                $this->cache[$shopId][$key] = $data;
            }

            public function get(string $key, int $shopId): array
            {
                return $this->cache[$shopId][$key];
            }

            public function exists(string $key, int $shopId): bool
            {
                return isset($this->cache[$shopId][$key]);
            }

            public function invalidateAll(): void
            {
                // TODO: Implement invalidateAll() method.
            }
        };
    }
}
