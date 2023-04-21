<?php

namespace OxidEsales\Smarty\Tests\Unit\SystemRequirements;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Smarty\Exception\TemplateFileNotFoundException;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\Smarty\SystemRequirements\MissingTemplateBlocksChecker;
use OxidEsales\Smarty\Loader\TemplateLoaderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class MissingTemplateBlocksCheckerTest extends TestCase
{
    use ContainerTrait;

    /**
     * @dataProvider dataProviderCollectMissingTemplateBlockExtensions
     */
    public function testCollectMissingTemplateBlockExtensions(
        bool $templateExist,
        array $blockExtensions,
        array $result
    ): void {
        $templateContent = '[{block name="block1"}][{/block}][{block name="block2"}][{/block}]';

        $missingTemplateBlockChecker = new MissingTemplateBlocksChecker(
            $this->getTemplateBlockExtensionDaoMock($blockExtensions),
            $this->getContextMock(),
            $this->getTemplateLoaderMock($templateExist, $templateContent),
            $this->getTemplateLoaderMock($templateExist, $templateContent),
            $this->getShopAdapterMock()
        );

        $this->assertEquals($result, $missingTemplateBlockChecker->collectMissingTemplateBlockExtensions());
    }

    /**
     * @return array
     */
    public function dataProviderCollectMissingTemplateBlockExtensions(): array
    {
        $blockExtension = new TemplateBlockExtension();
        $blockExtension->setName('block1');
        $blockExtension->setExtendedBlockTemplatePath('testTemplateName');
        $blockExtension->setModuleId('testModule1');

        $blockExtension2 = new TemplateBlockExtension();
        $blockExtension2->setName('block2');
        $blockExtension2->setExtendedBlockTemplatePath('testTemplateName');
        $blockExtension2->setModuleId('testModule2');

        $blockExtension3 = new TemplateBlockExtension();
        $blockExtension3->setName('block3');
        $blockExtension3->setExtendedBlockTemplatePath('testTemplateName');
        $blockExtension3->setModuleId('testModule3');

        return [
            [true, [$blockExtension, $blockExtension2], []],
            [false, [$blockExtension], [
                [
                    'module'   => 'testModule1',
                    'block'    => 'block1',
                    'template' => 'testTemplateName'
                ]]
            ],
            [true, [$blockExtension, $blockExtension2, $blockExtension3],[
                [
                    'module'   => 'testModule3',
                    'block'    => 'block3',
                    'template' => 'testTemplateName'
                ]]
            ]
        ];
    }

    private function getTemplateBlockExtensionDaoMock(array $blockExtensions): TemplateBlockExtensionDaoInterface
    {
        $daoMock = $this
            ->getMockBuilder(TemplateBlockExtensionDaoInterface::class)
            ->getMock();

        $daoMock
            ->method('getExtensionsByTheme')
            ->willReturn($blockExtensions);

        return $daoMock;
    }

    private function getContextMock(): ContextInterface
    {
        $contextMock = $this
            ->getMockBuilder(ContextInterface::class)
            ->getMock();

        $contextMock
            ->method('getCurrentShopId')
            ->willReturn(1);

        return $contextMock;
    }

    private function getTemplateLoaderMock(bool $templateExist, string $templateContent): TemplateLoaderInterface
    {
        $templateLoader = $this->getMockBuilder(TemplateLoaderInterface::class)
            ->getMock();
        if ($templateExist) {
            $templateLoader
                ->method('findTemplate')
                ->with('testTemplateName')
                ->willReturn($templateContent);
        } else {
            $templateLoader
                ->method('findTemplate')
                ->with('testTemplateName')
                ->willThrowException($this->get(TemplateFileNotFoundException::class));
        }

        $templateLoader
            ->method('getContext')
            ->willReturn($templateContent);
        return $templateLoader;
    }

    private function getShopAdapterMock(): ShopAdapterInterface
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)
            ->getMock();
        $shopAdapter
            ->method('getActiveThemeId')
            ->willReturn('testTheme');
        return $shopAdapter;
    }
}
