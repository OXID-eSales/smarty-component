<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Smarty\Bridge\SmartyEngineBridge;
use OxidEsales\Smarty\SmartyContextInterface;
use OxidEsales\Smarty\SmartyEngine;
use Smarty;

final class SmartyEngineTest extends IntegrationTestCase
{
    public function testExists(): void
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertTrue($engine->exists($template));
    }

    public function testExistsWithNonExistentTemplates(): void
    {
        $engine = $this->getEngine();

        $this->assertFalse($engine->exists('foobar'));
    }

    public function testRender(): void
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertFileExists($template);
        $this->assertSame('Hello OXID!', $engine->render($template));
    }

    public function testRenderWithContext(): void
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();

        $this->assertFileExists($template);
        $this->assertSame('Hello Test!', $engine->render($template, ['title' => 'Hello Test!']));
    }

    public function testRenderWithCacheId(): void
    {
        $template = $this->getTemplateDirectory() . 'smartyTemplate.tpl';

        $engine = $this->getEngine();
        $context = ['title' => 'Hello Test!', 'oxEngineTemplateId' => md5('smartyTemplate.tpl')];

        $this->assertFileExists($template);
        $this->assertSame('Hello Test!', $engine->render($template, $context));
        $this->assertSame('Hello Test!', $engine->render($template, $context));
    }

    public function testAddAndGetGlobals(): void
    {
        $engine = $this->getEngine();
        $engine->addGlobal('testGlobal', 'testValue');
        $this->assertSame(['testGlobal' => 'testValue'], $engine->getGlobals());
        $this->assertSame('testValue', $engine->_tpl_vars['testGlobal']);
    }

    public function testRenderFragment(): void
    {
        $fragment = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}][{$title}]';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame('Hello Test!', $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentWithSpecialCharacters(): void
    {
        $fragment = '[{$title}] Nekilnojamojo turto agentūrų verslo sėkme Литовские европарламентарии';
        $renderedFragment = 'Hello Test! Nekilnojamojo turto agentūrų verslo sėkme Литовские европарламентарии';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame($renderedFragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentNotAllowedToParseSmarty(): void
    {
        $fragment = '[{assign var=\'title\' value=$title|default:\'Hello OXID!\'}][{$title}]';
        $context = ['title' => 'Hello Test!'];
        $smarty = new Smarty();
        $engine = new SmartyEngine(
            $smarty,
            new SmartyEngineBridge(),
            $this->get(TemplateFileResolverInterface::class),
            $this->getSmartyContextMock()
        );
        $this->assertSame($fragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testRenderFragmentNoSmartyTagsAdded(): void
    {
        $fragment = '{assign var=\'title\' value=$title|default:\'Hello OXID!\'}{$title}';
        $context = ['title' => 'Hello Test!'];

        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $this->assertSame($fragment, $engine->renderFragment($fragment, 'ox:testid', $context));
    }

    public function testMagicSetterAndGetter(): void
    {
        $engine = $this->get('smarty.smarty_engine_factory')->getTemplateEngine();
        $engine->_tpl_vars = 'testValue';
        $this->assertSame('testValue', $engine->_tpl_vars);
    }

    private function getEngine(): SmartyEngine
    {
        $smarty = new Smarty();
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

    private function getTemplateDirectory(): string
    {
        return __DIR__ . '/Fixtures/';
    }
}
