<?php

declare(strict_types=1);

namespace Project\ButtonColorChange\Helper;

class ButtonColorHelper
{
    public function isValidHex(string $color): bool
    {
        return (bool) preg_match('/^#?[A-Fa-f0-9]{6}$/', $color);
    }
}
