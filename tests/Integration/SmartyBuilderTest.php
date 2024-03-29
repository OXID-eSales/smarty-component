<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\Smarty\Configuration\SmartyPluginsDataProviderInterface;
use OxidEsales\Smarty\Configuration\SmartyConfigurationFactoryInterface;
use OxidEsales\Smarty\SmartyBuilder;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Prophecy\PhpUnit\ProphecyTrait;
use Smarty;
use Symfony\Component\Filesystem\Filesystem;

final class SmartyBuilderTest extends IntegrationTestCase
{
    use ProphecyTrait;

    private mixed $debugMode;

    public function setup(): void
    {
        parent::setUp();
        $this->debugMode = Registry::getConfig()->getConfigParam('iDebug');
        new Smarty();
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('iDebug', $this->debugMode);
        parent::tearDown();
    }

    /**
     * @dataProvider smartySettingsDataProvider
     */
    public function testSmartySettingsAreSetCorrect(int $securityMode): void
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blDemoShop', $securityMode);
        $config->setConfigParam('iDebug', 0);
        $cachePath = (new ContextStub())->getTemplateCacheDirectory();
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->exists($cachePath)->willReturn(true);

        $configuration = $this->get(SmartyConfigurationFactoryInterface::class)->getConfiguration();
        $smarty = (new SmartyBuilder($filesystem->reveal()))
            ->setSettings($configuration->getSettings())
            ->setTemplateCompilePath($cachePath)
            ->setSecuritySettings($configuration->getSecuritySettings())
            ->registerPlugins($configuration->getPlugins())
            ->registerPrefilters($configuration->getPrefilters())
            ->registerResources($configuration->getResources())
            ->getSmarty();

        $smartySettings = $securityMode ?
            $this->getSmartySettingsWithSecurityOn() :
            $this->getSmartySettingsWithSecurityOff();
        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals(
                $varValue,
                $smarty->$varName,
                "Assertion failed for the Smarty setting: '$varName'"
            );
        }
    }

    /**
     * @return array
     */
    public function smartySettingsDataProvider(): array
    {
        return [
            'security on' => [1],
            'security off' => [0],
        ];
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        $config = Registry::getConfig();
        $templateDir = $config->getTemplateDir();
        $shopId = $config->getShopId();
        return [
            'security' => true,
            'php_handling' => 2,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . 'template_cache',
            'cache_dir' => $config->getConfigParam('sCompileDir') . 'template_cache',
            'compile_id' => md5($templateDir . '__' . $shopId),
            'template_dir' => [$templateDir],
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ],
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        $config = Registry::getConfig();
        $templateDir = $config->getTemplateDir();
        $shopId = $config->getShopId();
        return [
            'security' => false,
            'php_handling' => $config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . 'template_cache',
            'cache_dir' => $config->getConfigParam('sCompileDir') . 'template_cache',
            'compile_id' => md5($templateDir . '__' . $shopId),
            'template_dir' => [$templateDir],
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartyPlugins(): array
    {
        $pluginProvider = $this->get(SmartyPluginsDataProviderInterface::class);
        return array_merge($pluginProvider->getPlugins(), ['plugins']);
    }
}
