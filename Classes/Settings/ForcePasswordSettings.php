<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Settings;

class ForcePasswordSettings
{
    public function __construct(
        private array $rawSettings = []
    ) {}

    public function getPasswordResetPage(): int
    {
        return $this->rawSettings['passwordResetPage'];
    }
}
