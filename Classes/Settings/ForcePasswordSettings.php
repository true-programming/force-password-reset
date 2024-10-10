<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Settings;

/*
 * This file is part of TYPO3 CMS-based extension force_password_reset.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class ForcePasswordSettings
{
    public function __construct(
        private array $rawSettings = []
    ) {}

    public function getPasswordResetPage(): int
    {
        return (int)$this->rawSettings['passwordResetPage'];
    }
}
