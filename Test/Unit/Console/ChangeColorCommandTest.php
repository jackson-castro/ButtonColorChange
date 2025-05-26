<?php

declare(strict_types=1);

namespace Project\ButtonColorChange\Test\Unit\Console;

use Project\ButtonColorChange\Console\ChangeColorCommand;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use PHPUnit\Framework\TestCase;
use Project\ButtonColorChange\Helper\ButtonColorHelper;
use Symfony\Component\Console\Tester\CommandTester;

class ChangeColorCommandTest extends TestCase
{
    public function testExecuteWithValidInput(): void
    {
        $writer = $this->createMock(WriterInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $state = $this->createMock(State::class);
        $buttonColorHelper = $this->createMock(ButtonColorHelper::class);

        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getName')->willReturn('Default Store View');

        $storeManager->method('getStore')->with(1)->willReturn($store);
        $buttonColorHelper->method('isValidHex')->with('ff0000')->willReturn(true);

        $writer->expects($this->once())
            ->method('save')
            ->with('project/button_color_change/color', '#ff0000', 'stores', 1);

        $command = new ChangeColorCommand($state, $storeManager, $writer, $buttonColorHelper);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'hex' => 'ff0000',
            'store_id' => '1',
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Cor #ff0000 aplicada para a store', $commandTester->getDisplay());
    }

    public function testExecuteWithInvalidHex(): void
    {
        $writer = $this->createMock(WriterInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $state = $this->createMock(State::class);
        $buttonColorHelper = $this->createMock(ButtonColorHelper::class);

        $buttonColorHelper->method('isValidHex')->with('invalidhex')->willReturn(false);

        $command = new ChangeColorCommand($state, $storeManager, $writer, $buttonColorHelper);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'hex' => 'invalidhex',
            'store_id' => '1',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('Código Hexadecimal inexistente', $commandTester->getDisplay());
    }

    public function testExecuteWithInvalidStoreId(): void
    {
        $writer = $this->createMock(WriterInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $state = $this->createMock(State::class);
        $buttonColorHelper = $this->createMock(ButtonColorHelper::class);

        $buttonColorHelper->method('isValidHex')->with('ff0000')->willReturn(true);

        $storeManager->method('getStore')->with(999)->willThrowException(
            new \Magento\Framework\Exception\NoSuchEntityException(__('Store not found'))
        );

        $command = new ChangeColorCommand($state, $storeManager, $writer, $buttonColorHelper);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'hex' => 'ff0000',
            'store_id' => '999',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('Store-view inexistente', $commandTester->getDisplay());
    }
}
