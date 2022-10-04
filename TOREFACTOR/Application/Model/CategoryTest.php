<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxCategory;
use oxCategoryHelper;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxUtilsView;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxCategoryHelper.php';

class CategoryTest extends \OxidTestCase
{
    protected $_oCategoryA = null;
    protected $_oCategoryB = null;

    protected $_sAttributeA;
    protected $_sAttributeB;
    protected $_sAttributeC;
    protected $_sAttributeD;

    protected $_sCategory = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->removeTestData();
        $this->saveParent();
        $this->saveChild();
        $this->_sAttributeC = '8a142c3ee0edb75d4.80743302';
        $this->_sAttributeB = '8a142c3f0a792c0c3.93013584';
        $this->_sCategory = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';
        $this->_sAttributeD = $this->getTestConfig()->getShopEdition() == 'EE' ? '8a142c3f0a792c0c3.93013584' : '8a142c3e9cd961518.80299776';
        $db = oxDb::getDb();
        $db->Execute('insert into oxcategory2attribute (oxid, oxobjectid, oxattrid, oxsort) values ("test3","' . $this->_sCategory . '","' . $this->_sAttributeD . '", "333")');
    }

    protected function tearDown(): void
    {
        $this->removeTestData();
        parent::tearDown();
    }

    private function removeTestData()
    {
        $db = oxDb::getDb();
        $sDelete = "Delete from oxcategories where oxid like 'test%'";
        $db->Execute($sDelete);
        $sDelete = "Delete from oxcategory2attribute where oxid like 'test%' ";
        $db->Execute($sDelete);

        $this->cleanUpTable("oxattribute");
        $this->cleanUpTable("oxobject2attribute");
    }

    /**
     * initialize parent obj
     */
    private function saveParent()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test','test','{$sShopId}','1','4','test','','','','','1','10','50')";
        $this->addToDatabase($sInsert, 'oxcategories');

        $this->_oCategory = oxNew('oxcategory');
        $this->_oCategory->load('test');
    }

    /**
     * initialize child obj
     */
    private function saveChild()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXPARENTID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test2','test','" . $sShopId . "','test','2','3','test','','','','','1','10','50')";
        $this->addToDatabase($sInsert, 'oxcategories');

        $this->_oCategoryB = oxNew('oxcategory');
        $this->_oCategoryB->load('test2');
    }

    /**
     * safely reloads test objects
     */
    private function reload()
    {
        if (@$this->_oCategory->getId()) {
            $oObj = oxRegistry::getUtilsObject()->oxNew("oxCategory", "core");
            $oObj->load($this->_oCategory->getId());
            $this->_oCategory = $oObj;
        }
        if (@$this->_oCategoryB->getId()) {
            $oObj = oxRegistry::getUtilsObject()->oxNew("oxCategory", "core");
            $oObj->load($this->_oCategoryB->getId());
            $this->_oCategoryB = $oObj;
        }
    }

    public function testAssign()
    {
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', false);
        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', false);
        $this->_oCategory->oxcategories__oxlongdesc = new oxField('aa[{* smarty comment *}]zz', oxField::T_RAW);
        $this->_oCategory->save();
        $sDimagedir = $this->getConfig()->getPictureUrl(null, false, $this->getConfig()->isSsl(), null);
        $this->reload();
        $this->assertEquals('aa[{* smarty comment *}]zz', $this->_oCategory->oxcategories__oxlongdesc->value);
        $this->assertEquals(0, $this->_oCategory->getNrOfArticles());
        $this->assertEquals($sDimagedir, $this->_oCategory->dimagedir);
    }

    public function testAssignParseLongDescInList()
    {
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', true);

        $this->_oCategory->oxcategories__oxlongdesc = new oxField('aa[{* smarty comment *}]zz', oxField::T_RAW);
        $this->_oCategory->setId('test33');
        $this->_oCategory->save();
        $oObj3 = oxNew("oxCategory");
        $oObj3->setInList();
        $oObj3->load($this->_oCategory->getId());
        //NOT parsed
        $this->assertEquals('aa[{* smarty comment *}]zz', $oObj3->oxcategories__oxlongdesc->value);
    }

}
