<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use oxModuleList;
use PHPUnit\Framework\MockObject\MockObject;

final class ModuleRemoveTest extends BaseModuleTestCase
{
    public function providerModuleDeactivation(): array
    {
        return [
            $this->caseRemoveOneExtensionOfOneModule(),
            $this->caseRemoveAllExtensionsOfOneModule(),
            $this->caseRemoveOneExtensionWithMetadataV2(),
        ];
    }

    /**
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleRemove(
        array $aInstallModules,
        array $aRemovedExtensions,
        array $aResultToAssert
    ): void {
        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id);
        }

        /** @var oxModuleList|MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, ['getDeletedExtensions']);
        $oModuleList->expects($this->any())->method('getDeletedExtensions')->will($this->returnValue($aRemovedExtensions));

        $oModuleList->cleanup();
        $this->runAsserts($aResultToAssert);
    }

    /**
     * @group quarantine
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleRemoveInSubShop(
        array $aInstallModules,
        array $aRemovedExtensions,
        array $aResultToAssert
    ): void {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }

        $this->prepareProjectConfigurationWitSubshops();

        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id, 1);
        }

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsObject::class, null);

        $oEnvironment = new Environment();
        $oEnvironment->setShopId(2);
        $_POST['shp'] = 2;

        foreach ($aInstallModules as $id) {
            $this->installAndActivateModule($id, 2);
        }

        /** @var oxModuleList|MockObject $oModuleList */
        $oModuleList = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleList::class, ['getDeletedExtensions']);
        $oModuleList->expects($this->any())->method('getDeletedExtensions')->will($this->returnValue($aRemovedExtensions));

        $oModuleList->cleanup();

        //Assert on subshop
        $this->runAsserts($aResultToAssert);

        $this->markTestIncomplete('Skipped till cleanup for subshops will be fixed');

        //Assert on main shop
        $oEnvironment->setShopId(1);
        $this->runAsserts($aResultToAssert);
    }

    private function caseRemoveOneExtensionOfOneModule(): array
    {
        return [
            // modules to be activated during test preparation
            [
                'extending_3_blocks',
                'with_everything'
            ],

            // extensions that will be removed
            [
                'with_everything' => [
                    'extensions' => [
                        'oxuser' => 'with_everything/myuser',
                    ]
                ]
            ],

            // environment asserts
            [
                'blocks'          => [
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                ],
                'disabledModules' => [],
                'versions'        => [
                    'extending_3_blocks' => '1.0',
                ],
            ]
        ];
    }

    private function prepareProjectConfigurationWitSubshops()
    {
        $projectConfigurationDao = $this->getContainer()->get(ProjectConfigurationDaoInterface::class);
        $projectConfiguration = $projectConfigurationDao->getConfiguration();

        $projectConfiguration->addShopConfiguration(2, new ShopConfiguration());


        $projectConfigurationDao->save($projectConfiguration);
    }
}
