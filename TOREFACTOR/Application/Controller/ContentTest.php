<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Application\Model\PaymentList;
use OxidEsales\EshopCommunity\Application\Model\DeliverySetList;
use OxidEsales\EshopCommunity\Application\Model\Delivery;
use OxidEsales\EshopCommunity\Application\Model\DeliveryList;
use \oxUtilsView;
use \oxField;
use \Exception;
use \oxcontent;
use \stdClass;
use \oxDb;
use \oxTestModules;

class ContentTest extends \OxidTestCase
{
    /** @var oxContent  */
    protected $_oObj = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_oObj = oxNew('oxbase');
        $this->_oObj->init('oxcontents');
        $this->_oObj->oxcontents__oxtitle = new oxField('test', oxField::T_RAW);
        $sShopId = $this->getConfig()->getShopId();
        $this->_oObj->oxcontents__oxshopid = new oxField($sShopId, oxField::T_RAW);
        $this->_oObj->oxcontents__oxloadid = new oxField('_testLoadId', oxField::T_RAW);
        $this->_oObj->oxcontents__oxcontent = new oxField("testcontentDE&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        //$this->_oObj->oxcontents__oxcontent = new oxField('[{ $oxcmp_shop->oxshops__oxowneremail->value }]', oxField::T_RAW);
        $this->_oObj->oxcontents__oxcontent_1 = new oxField("testcontentENG&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        $this->_oObj->oxcontents__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oObj->oxcontents__oxactive_1 = new oxField('1', oxField::T_RAW);
        $this->_oObj->save();

        $sOxid = $this->_oObj->getId();

        $this->_oObj = oxNew('oxcontent');
        $this->_oObj->load($sOxid);
    }

    protected function tearDown(): void
    {
        $this->_oObj->delete();
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxdel2delset');
        parent::tearDown();
    }

    /**
     * Content::testGetParsedContent() Test case
     *
     * Add bugfix to #0004298: If there is smarty tag in content, then it is saved in same name template.
     */
    public function testGetParsedContent()
    {
        $this->_oObj->oxcontents__oxcontent = new oxField("[{ 'A'|cat:'B' }]SSSSSSSS", oxField::T_RAW);
        $this->_oObj->save();
        $this->setRequestParameter('oxcid', $this->_oObj->getId());
        $oContent = oxNew('content');

        $this->assertEquals('ABSSSSSSSS', $oContent->getParsedContent(), 'Result from smarty not same as in content page.');

        // Check if second CMS page will be generated with different content.
        $oSecond = oxNew('oxcontent');
        $oSecond->setId('_test_testGetParsedContent');
        $oSecond->oxcontents__oxtitle = new oxField('test', oxField::T_RAW);
        $sShopId = $this->getConfig()->getShopId();
        $oSecond->oxcontents__oxshopid = new oxField($sShopId, oxField::T_RAW);
        $oSecond->oxcontents__oxloadid = new oxField('_testLoadId_testGetParsedContent', oxField::T_RAW);
        $oSecond->oxcontents__oxcontent = new oxField("[{ 'A'|cat:'D' }]SSSSSSSS", oxField::T_RAW);
        $oSecond->oxcontents__oxcontent_1 = new oxField("testcontentENG&, &, !@#$%^&*%$$&@'.,;p\"ss", oxField::T_RAW);
        $oSecond->oxcontents__oxactive = new oxField('1', oxField::T_RAW);
        $oSecond->oxcontents__oxactive_1 = new oxField('1', oxField::T_RAW);
        $oSecond->save();
        $this->setRequestParameter('oxcid', $oSecond->getId());
        $oContent = oxNew('content');

        $this->assertEquals('ADSSSSSSSS', $oContent->getParsedContent(), 'Content not as in second page. If result ABSSSSSSSS than it is ame as in first page, so used wrong smarty cache file.');
    }

    /**
     * getParsedContent() test case
     * test returned parsed content with smarty tags when template regeneration is disabled
     * and template is saved twice.
     *
     * @return null
     */
    public function testGetParsedContentTagsWhenTemplateAlreadyGeneratedAndRegenerationDisabled()
    {
        $this->getConfig()->setConfigParam('blCheckTemplates', false);

        $this->_oObj->oxcontents__oxcontent = new oxField("[{* *}]generated", oxField::T_RAW);
        $this->_oObj->save();
        $this->setRequestParameter('oxcid', $this->_oObj->getId());

        $oContent = oxNew('content');
        $oContent->getParsedContent();

        $this->_oObj->oxcontents__oxcontent = new oxField("[{* *}]regenerated", oxField::T_RAW);
        $this->_oObj->save();

        $oContent = oxNew('content');
        $this->assertEquals('regenerated', $oContent->getParsedContent());
    }
}
