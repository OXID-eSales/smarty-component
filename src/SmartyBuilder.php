<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty;

use Symfony\Component\Filesystem\Filesystem;

class SmartyBuilder implements SmartyBuilderInterface
{
    /**
     * @var \Smarty
     */
    private $smarty;

    public function __construct(
        private Filesystem $filesystem)
    {
        $this->smarty = new \Smarty();
    }

    /**
     * Sets properties of smarty object.
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            $this->smarty->$key = $value;
        }
        return $this;
    }

    public function setTemplateCompilePath(string $compilePath): self
    {
        if (!$this->filesystem->exists($compilePath)) {
            $this->filesystem->mkdir($compilePath);
        }

        $this->smarty->compile_dir = $compilePath;
        $this->smarty->cache_dir = $compilePath;

        return $this;
    }

    /**
     * Sets security options of smarty object.
     *
     * @param array $settings
     *
     * @return self
     */
    public function setSecuritySettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        $originalSettings = $this->smarty->{$key}[$subKey];
                        $this->smarty->{$key}[$subKey] = array_merge($originalSettings, $subValue);
                    } else {
                        $this->smarty->{$key}[$subKey] = $subValue;
                    }
                }
            } else {
                $this->smarty->$key = $value;
            }
        }
        return $this;
    }

    /**
     * Registers a resource of smarty object.
     *
     * @param array $resourcesToRegister
     *
     * @return self
     */
    public function registerResources(array $resourcesToRegister)
    {
        foreach ($resourcesToRegister as $key => $resources) {
            $this->smarty->register_resource($key, $resources);
        }
        return $this;
    }

    /**
     * Register prefilters of smarty object.
     *
     * @param array $prefilters
     *
     * @return self
     */
    public function registerPrefilters(array $prefilters)
    {
        foreach ($prefilters as $prefilter => $path) {
            if (file_exists($path)) {
                include_once $path;
                $this->smarty->register_prefilter($prefilter);
            }
        }
        return $this;
    }

    /**
     * Register plugins of smarty object.
     *
     * @param array $plugins
     *
     * @return self
     */
    public function registerPlugins(array $plugins)
    {
        if (is_array($plugins)) {
            $this->smarty->plugins_dir = array_merge(
                $plugins,
                $this->smarty->plugins_dir
            );
        }
        return $this;
    }

    /**
     * @return \Smarty
     */
    public function getSmarty(): \Smarty
    {
        return $this->smarty;
    }
}
