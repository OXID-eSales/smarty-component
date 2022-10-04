<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test-module',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        'shopClass' => 'testModuleClassExtendsShopClass',
    ],
    'smartyPluginDirectories'  => [
        'SmartyPlugins/directory1',
        'SmartyPlugins/directory2',
    ],
    'blocks'                  => [
        [
            'theme'    => 'theme_id',
            'template' => 'template_1.tpl',
            'block'    => 'block_1',
            'file'     => '/blocks/template_1.tpl',
            'position' => '1'
        ],
        [
            'template' => 'template_2.tpl',
            'block'    => 'block_2',
            'file'     => '/blocks/template_2.tpl',
            'position' => '2'
        ],
    ],
    'settings' => [
        [
            'group' => 'main',
            'name' => 'test-setting',
            'type' => 'arr',
            'value' => ['Preis', 'Hersteller'],
        ],
        [
            'name' => 'string-setting',
            'type' => 'str',
            'value' => 'default',
        ]
    ],
);
