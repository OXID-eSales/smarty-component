<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxField;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Facts\Facts;
use oxRegistry;
use oxRssFeed;
use oxTestModules;
use stdClass;

class RssfeedTest extends \OxidTestCase
{
    public function testGetArticleItems()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", false);

        $oCfg = $this->getConfig();
        $oCfg->setConfigParam('aCurrencies', array('EUR@1.00@.@.@EUR@1'));
        $oRss = oxNew('oxRssFeed');
        Registry::set(Config::class, $oCfg);

        $oLongDesc = new stdClass();
        $oLongDesc->value = "artlogndesc";

        $oArt1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getLink", "getLongDescription"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oLongDesc2 = new stdClass();
        $oLongDesc2->value = " &nbsp;<div>";

        $oArt2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getLink", "getLongDescription"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc2));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');
        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1 20.0 EUR';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2 10.0 EUR';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->getArticleItems($oArr));
    }

    public function testGetArticleItemsDescriptionParsedWithSmarty()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", true);

        $oCfg = $this->getConfig();
        $oCfg->setConfigParam('aCurrencies', array('EUR@1.00@.@.@EUR@1'));
        $oRss = oxNew('oxRssFeed');
        Registry::set(Config::class, $oCfg);

        $oArt1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getLink", "getLongDescription"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDescription')->will($this->returnValue(new oxField("artlogndesc", oxField::T_RAW)));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oArt2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array("getLink", "getLongDescription"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDescription')->will($this->returnValue(new oxField(" &nbsp;<div>", oxField::T_RAW)));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');
        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1 20.0 EUR';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2 10.0 EUR';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->getArticleItems($oArr));
    }
}
