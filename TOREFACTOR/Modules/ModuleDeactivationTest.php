<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

class ModuleDeactivationTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleDeactivation()
    {
        return array(
            $this->caseSevenModulesPreparedDeactivatedWithEverything()
        );
    }

    /**
     * Test check shop environment after module deactivation
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $aInstallModules
     * @param string $sModuleId
     * @param array  $aResultToAssert
     */
    public function testModuleDeactivation($aInstallModules, $sModuleId, $aResultToAssert)
    {
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');
        $this->deactivateModule($oModule, $sModuleId);

        $this->runAsserts($aResultToAssert);
    }

    /**
     * Test check shop environment after module deactivation in subshop.
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $aInstallModules
     * @param string $sModuleId
     * @param array  $aResultToAssert
     */
    public function testModuleDeactivationInSubShop($aInstallModules, $sModuleId, $aResultToAssert)
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $this->markTestSkipped("This test case is only actual when SubShops are available.");
        }

        $this->prepareProjectConfigurationWitSubshops();

        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');

        $oEnvironment = new Environment();
        $oEnvironment->setShopId(2);
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId, 2);
        }

        $this->deactivateModule($oModule, $sModuleId, 2);

        $this->runAsserts($aResultToAssert);
    }

    /**
     * Data provider case with 7 modules prepared and with_everything module deactivated
     *
     * @return array
     */
    private function caseSevenModulesPreparedDeactivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_3_blocks', 'with_everything'
            ),

            // module that will be deactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'disabledModules' => array(
                    'with_everything'
                ),
                'templates'       => array(
                ),
                'versions'        => array(
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
            )
        );
    }

    private function prepareProjectConfigurationWitSubshops()
    {
        $projectConfigurationDao = $this->getContainer()->get(ProjectConfigurationDaoInterface::class);
        $projectConfiguration = $projectConfigurationDao->getConfiguration();

        $projectConfiguration->addShopConfiguration(2, new ShopConfiguration());


        $projectConfigurationDao->save($projectConfiguration);
    }
}
