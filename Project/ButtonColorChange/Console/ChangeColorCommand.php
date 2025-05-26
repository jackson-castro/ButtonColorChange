<?php

declare(strict_types=1);

namespace Project\ButtonColorChange\Console;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Project\ButtonColorChange\Helper\ButtonColorHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeColorCommand extends Command
{
    private const CONFIG_PATH = 'project/button_color_change/color';

    public function __construct(
        private readonly State $state,
        private readonly StoreManagerInterface $storeManager,
        private readonly WriterInterface $configWriter,
        private readonly ButtonColorHelper $buttonColorHelper
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('color:change')
            ->setDescription('Muda a cor dos botões de uma store-view.')
            ->addArgument('hex', InputArgument::REQUIRED, 'Código Hexadecimal da cor (ex: ff0000)')
            ->addArgument('store_id', InputArgument::REQUIRED, 'ID da store-view');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hex = $input->getArgument('hex');
        $storeId = (int) $input->getArgument('store_id');

        if (!$this->buttonColorHelper->isValidHex($hex)) {
            $output->writeln('<error>Código Hexadecimal inexistente. Digite um válido.</error>');
            return Command::FAILURE;
        }

        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (\Exception $e) {
            $output->writeln('<error>Store-view inexistente. Digite um Id válido</error>');
            return Command::FAILURE;
        }

        $hex = '#' . ltrim($hex, '#');
        $this->configWriter->save(self::CONFIG_PATH, $hex, 'stores', $storeId);

        $output->writeln("<info>Cor $hex aplicada para a store '{$store->getName()}'.</info>");
        return Command::SUCCESS;
    }

}