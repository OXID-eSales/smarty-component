<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Smarty\Bridge\SmartyEngineBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

class SmartyEngine implements TemplateEngineInterface, SmartyEngineInterface
{
    /**
     * Array of global parameters
     *
     * @var array
     */
    private $globals = [];

    public function __construct(
        private \Smarty $engine,
        private SmartyEngineBridgeInterface $bridge,
        private TemplateFileResolverInterface $templateFileResolver,
        private SmartyContextInterface $context
    ) {
    }

    /**
     * Renders a template.
     *
     * @param string $name    A template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $context = []): string
    {
        $templateFileName = $this->templateFileResolver->getFilename($name);

        foreach ($context as $key => $value) {
            $this->engine->assign($key, $value);
        }
        if (isset($context['oxEngineTemplateId'])) {
            return $this->engine->fetch($templateFileName, $context['oxEngineTemplateId']);
        }

        return $this->engine->fetch($templateFileName);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The Id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        if ($this->doNotRender($fragment)) {
            return $fragment;
        }
        return $this->bridge->renderFragment($this->engine, $fragment, $fragmentId, $context);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool
    {
        $templateFileName = $this->templateFileResolver->getFilename($name);
        return $this->engine->template_exists($templateFileName);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal(string $name, $value)
    {
        $this->globals[$name] = $value;
        $this->engine->assign($name, $value);
    }

    /**
     * Returns assigned globals.
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->globals;
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name  The name of the parameter.
     * @param mixed  $value The value of the parameter.
     */
    public function __set($name, $value)
    {
        if (property_exists($this->engine, $name)) {
            $this->engine->$name = $value;
        }
    }

    /**
     * Pass parameters to the Smarty instance.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->engine->$name;
    }

    /**
     * @return \Smarty
     */
    public function getSmarty(): \Smarty
    {
        return $this->engine;
    }

    /**
     * @param \Smarty $smarty
     */
    public function setSmarty(\Smarty $smarty)
    {
        $this->engine = $smarty;
    }

    private function doNotRender(string $fragment): bool
    {
        return !str_contains($fragment, "[{") || $this->context->isSmartyForContentDeactivated();
    }
}
