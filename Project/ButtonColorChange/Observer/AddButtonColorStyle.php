<?php

declare(strict_types=1);

namespace Project\ButtonColorChange\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AddButtonColorStyle implements ObserverInterface
{
    private const CONFIG_PATH = 'project/button_color_change/color';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function execute(Observer $observer): void
    {

        /** @var LayoutInterface $layout */
        $layout = $observer->getData('layout');

        $color = $this->scopeConfig->getValue(
            self::CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );

        if (!$color) {
            return;
        }

        $style = sprintf(
            '<style>
                button, .btn, .action.primary {
                    background-color: %1$s !important;
                    border-color: %1$s !important;
                }
            </style>',
            $color
        );

        $headBlock = $layout->getBlock('head.additional') ?: $layout->getBlock('head');
        if ($headBlock) {
            $headBlock->append(
                $layout->createBlock(\Magento\Framework\View\Element\Text::class)
                    ->setText($style)
            );
        }
    }
}