<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Registry;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_nocache.php
 * Type: string, html
 * Name: oxid_nocache
 * Purpose: Inserts Items not cached
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_insert_oxid_nocache($params, &$smarty)
{
    $smarty->caching = false;

    // #1184M - specialchar search
    $sSearchParamForHTML = Registry::getRequest()->getRequestEscapedParameter("searchparam");
    $sSearchParamForLink = rawurlencode(Registry::getRequest()->getRequestParameter("searchparam"));
    if ($sSearchParamForHTML) {
        $smarty->assign_by_ref("searchparamforhtml", $sSearchParamForHTML);
        $smarty->assign_by_ref("searchparam", $sSearchParamForLink);
    }

    $sSearchCat = Registry::getRequest()->getRequestEscapedParameter("searchcnid");
    if ($sSearchCat) {
        $smarty->assign_by_ref("searchcnid", rawurldecode($sSearchCat));
    }

    foreach (array_keys($params) as $key) {
        $viewData = & $params[$key];
        $smarty->assign_by_ref($key, $viewData);
    }

    $sOutput = $smarty->fetch($params['tpl']);

    $smarty->caching = false;

    return $sOutput;
}
