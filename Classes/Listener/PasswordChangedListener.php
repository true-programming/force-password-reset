<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Listener;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\FrontendLogin\Event\PasswordChangeEvent;

class PasswordChangedListener
{
    public function __construct(
        protected ConnectionPool $connectionPool
    ) {}

    public function __invoke(PasswordChangeEvent $event): void
    {
        $user = $event->getUser();
        $qb = $this->connectionPool->getQueryBuilderForTable('fe_users');
        $qb
            ->update('fe_users')
            ->set('tx_forcepasswordreset_force_password_reset', 0)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($user['uid'], Connection::PARAM_INT))
            )
            ->executeStatement();
    }
}
