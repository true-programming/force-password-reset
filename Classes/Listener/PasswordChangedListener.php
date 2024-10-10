<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Listener;

/*
 * This file is part of TYPO3 CMS-based extension force_password_reset.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Trueprogramming\ForcePasswordReset\Constants;
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
            ->set(Constants::PASSWORD_RESET_FIELD_NAME, 0)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($user['uid'], Connection::PARAM_INT))
            )
            ->executeStatement();
    }
}
