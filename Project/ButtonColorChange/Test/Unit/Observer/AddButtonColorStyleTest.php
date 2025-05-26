<?php

declare(strict_types=1);

namespace Project\ButtonColorChange\Test\Unit\Observer;

use Project\ButtonColorChange\Observer\AddButtonColorStyle;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Text;
use PHPUnit\Framework\TestCase;

class AddButtonColorStyleTest extends TestCase
{
    public function testExecuteInjectsStyleWhenColorIsSet(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $layout = $this->createMock(LayoutInterface::class);
        $textBlock = $this->createMock(Text::class);
        $observer = $this->createMock(Observer::class);

        $headBlock = $this->getMockBuilder(\Magento\Framework\View\Element\AbstractBlock::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['append'])
            ->getMockForAbstractClass();

        $scopeConfig->method('getValue')
            ->with('project/button_color_change/color', 'store')
            ->willReturn('#ff0000');

        $layout->expects($this->any())
            ->method('getBlock')
            ->willReturnCallback(function ($name) use ($headBlock) {
                return in_array($name, ['head.additional', 'head']) ? $headBlock : null;
            });

        $layout->expects($this->once())
            ->method('createBlock')
            ->with(Text::class)
            ->willReturn($textBlock);

        $textBlock->expects($this->once())
            ->method('setText')
            ->with($this->stringContains('#ff0000'))
            ->willReturnSelf();

        $headBlock->expects($this->once())
            ->method('append')
            ->with($textBlock);

        $observer->method('getData')
            ->with('layout')
            ->willReturn($layout);

        $observerInstance = new AddButtonColorStyle($scopeConfig);
        $observerInstance->execute($observer);
    }

    public function testExecuteSkipsWhenNoColorIsSet(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $observer = $this->createMock(Observer::class);

        $scopeConfig->method('getValue')
            ->with('project/button_color_change/color', 'store')
            ->willReturn(null);

        $observerInstance = new AddButtonColorStyle($scopeConfig);
        $observer->expects($this->any())->method('getData')->willReturn(null);

        $observerInstance->execute($observer);
    }
}
