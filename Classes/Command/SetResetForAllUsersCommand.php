<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Command;

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
