<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

final class ModuleActivationTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return [
            $this->caseReactivatedWithRemovedExtension()
        ];
    }

    /**
     * @dataProvider providerModuleActivation
     */
    public function testModuleActivation(array $aInstallModules, string $sModule, array $aResultToAsserts): void
    {
        foreach ($aInstallModules as $moduleId) {
            $this->installAndActivateModule($moduleId);
        }

        $oModule = oxNew('oxModule');
        $this->deactivateModule($oModule, $sModule);

        $this->installAndActivateModule($sModule);

        $this->runAsserts($aResultToAsserts);
    }

    private function caseReactivatedWithRemovedExtension(): array
    {
        return [

            // modules to be activated during test preparation
            [
                'with_everything',
                'extending_3_blocks'
            ],

            // module that will be reactivated
            'with_everything',

            // environment asserts
            [
                'blocks'          => [
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                    ['template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'],
                    ['template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'],
                ],
                'extend'          => [
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'oeTest/extending_1_class/myorder&with_everything/myorder1',
                    \OxidEsales\Eshop\Application\Model\Article::class => 'with_everything/myarticle',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'with_everything/myuser',
                ],
                'settings'        => [
                    ['group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'],
                    ['group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'],
                ],
                'disabledModules' => [],
                'templates'       => [
                    'with_2_templates' => [
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ],
                    'with_everything'  => [
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ],
                ],
                'events'          => [
                    'with_everything' => [
                        'onActivate'   => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onActivate',
                        'onDeactivate' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event\MyEvents::onDeactivate'
                    ],
                    'with_events' => [
                        'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onActivate',
                        'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_events\Event\MyEvents::onDeactivate'
                    ]
                ],
                'versions'        => [
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ],
            ]
        ];
    }
}
