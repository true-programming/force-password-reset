<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Command;

/*
 * This file is part of TYPO3 CMS-based extension force_password_reset.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trueprogramming\ForcePasswordReset\Constants;
use TYPO3\CMS\Core\Database\ConnectionPool;

class SetResetForAllUsersCommand extends Command
{
    public function __construct(
        private ConnectionPool $connectionPool,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $connection = $this->connectionPool->getConnectionForTable('fe_users');
            $connection->update(
                'fe_users',
                [
                    Constants::PASSWORD_RESET_FIELD_NAME => 1,
                ],
                [
                    'deleted' => 0,
                ]
            );
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
