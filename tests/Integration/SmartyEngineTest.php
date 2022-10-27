<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration;

use OxidEsales\Smarty\Bridge\SmartyEngineBridge;
use OxidEsales\Smarty\SmartyContext;
use OxidEsales\Smarty\SmartyContextInterface;
use OxidEsales\Smarty\SmartyEngine;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class SmartyEngineTest extends IntegrationTestCase
{

    public function testExists()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue($engine->exists($template));
    }

    public function testExistsWithNonExistentTemplates()
    {
        $engine = $this->getEngine();

        $this->assertFalse($engine->exists('foobar'));
    }

    public function testRender()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello OXID!', $engine->render($template));
    }

    public function testRenderWithContext()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello Test!', $engine->render($template, ['title' => 'Hello Test!']));
    }

    public function testRenderWithCacheId()
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();
        $context = ['title' => 'Hello Test!', 'oxEngineTemplateId' => md5('smartyTemplate.tpl')];

        $this->assertTrue(file_exists($template));
        $this->assertSame('Hello Test!', $engine->render($template, $context));
        $this->assertSame('Hello Test!', $engine->render($template, $context));
    }

    public function testAddAndGetGlobals()
    {
        $engine = $this->getEngine();
        $engine->addGlobal('testGlobal', 'testValue');
        $this->assertSame(['testGlobal' => 'testValue'], $engine->getGlobals());
        $this->assertSame('testValue', $engine->_tpl_vars['testGlobal']);
    }

    public function testRenderFragment()
    {
        $fragment = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}][{$title}]';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame('Hello Test!', $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentWithSpecialCharacters()
    {
        $fragment = '[{$title}] Nekilnojamojo turto agentūrų verslo sėkme Литовские европарламентарии, срок полномочий которых в 2009 году подходит к концу Der Umstieg war für uns ein voller Erfolg. OXID eShop ist flexibel und benutzerfreundlich';
        $renderedFragment = 'Hello Test! Nekilnojamojo turto agentūrų verslo sėkme Литовские европарламентарии, срок полномочий которых в 2009 году подходит к концу Der Umstieg war für uns ein voller Erfolg. OXID eShop ist flexibel und benutzerfreundlich';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame($renderedFragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentNotAllowedToParseSmarty()
    {
        $fragment = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}][{$title}]';
        $context = ['title' => 'Hello Test!'];
        $smarty = new \Smarty();
        $engine = new SmartyEngine(
            $smarty,
            new SmartyEngineBridge(),
            $this->get(TemplateFileResolverInterface::class),
            $this->getSmartyContextMock()
        );
        $this->assertSame($fragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentNoSmartyTagsAdded()
    {
        $fragment = '{assign var=\'title\' value=$title|default:\'Hello OXID!\'}{$title}';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame($fragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testMagicSetterAndGetter()
    {
        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $engine->_tpl_vars = 'testValue';
        $this->assertSame('testValue', $engine->_tpl_vars);
    }

    private function getEngine()
    {
        $smarty = new \Smarty();
        $smarty->compile_dir = sys_get_temp_dir();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';
        return new SmartyEngine(
            $smarty,
            new SmartyEngineBridge(),
            $this->get(TemplateFileResolverInterface::class),
            $this->get(SmartyContextInterface::class)
        );
    }

    private function getSmartyContextMock(): SmartyContextInterface
    {
        $context = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();
        $context
            ->method('isSmartyForContentDeactivated')
            ->willReturn(true);
        return $context;
    }

    private function getTemplateDirectory()
    {
        return __DIR__ . '/Fixtures/';
    }
}
