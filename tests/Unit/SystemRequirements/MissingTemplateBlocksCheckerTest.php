<?php


namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Smarty\SystemRequirements;


use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtension;
use OxidEsales\Smarty\Module\TemplateExtension\TemplateBlockExtensionDaoInterface;
use OxidEsales\Smarty\SystemRequirements\MissingTemplateBlocksChecker;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader\TemplateLoaderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

class MissingTemplateBlocksCheckerTest extends TestCase
{
    /**
     * @dataProvider dataProviderCollectMissingTemplateBlockExtensions
     */
    public function testCollectMissingTemplateBlockExtensions($templateExist, $blockExtensions, $result)
    {
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
    public function dataProviderCollectMissingTemplateBlockExtensions()
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
        $templateLoader->expects($this->any())
            ->method('exists')
            ->with('testTemplateName')
            ->will($this->returnValue($templateExist));
        $templateLoader->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue($templateContent));
        return $templateLoader;
    }

    private function getShopAdapterMock(): ShopAdapterInterface
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)
            ->getMock();
        $shopAdapter->expects($this->any())
            ->method('getActiveThemeId')
            ->will($this->returnValue('testTheme'));
        return $shopAdapter;
    }
}