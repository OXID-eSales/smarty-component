<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\TemplateExtension;

class TemplateBlockExtension
{
    private string $name;
    private string $filePath;
    private string $extendedBlockTemplatePath;
    private int $position = 1;
    private string $moduleId;
    private int $shopId;
    private string $themeId = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TemplateBlockExtension
    {
        $this->name = $name;
        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): TemplateBlockExtension
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getExtendedBlockTemplatePath(): string
    {
        return $this->extendedBlockTemplatePath;
    }

    public function setExtendedBlockTemplatePath(string $extendedBlockTemplatePath): TemplateBlockExtension
    {
        $this->extendedBlockTemplatePath = $extendedBlockTemplatePath;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): TemplateBlockExtension
    {
        $this->position = $position;
        return $this;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function setModuleId(string $moduleId): TemplateBlockExtension
    {
        $this->moduleId = $moduleId;
        return $this;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): TemplateBlockExtension
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function setThemeId(string $themeId): TemplateBlockExtension
    {
        $this->themeId = $themeId;
        return $this;
    }
}
