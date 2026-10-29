<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Middleware;

/*
 * This file is part of TYPO3 CMS-based extension force_password_reset.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Trueprogramming\ForcePasswordReset\Constants;
use Trueprogramming\ForcePasswordReset\Settings\ForcePasswordSettings;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ResetPasswordAfterLoginRedirectMiddleware implements MiddlewareInterface
{
    private const USERNAME_FORM_FIELD_NAME = 'user';
    private const LOGINTYPE_FORM_FIELD_NAME = 'logintype';
    private const FE_USER_TABLENAME = 'fe_users';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly ResponseFactory $responseFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $givenUserName = $parsedBody[self::USERNAME_FORM_FIELD_NAME] ?? null;
        $loginType = $parsedBody[self::LOGINTYPE_FORM_FIELD_NAME] ?? null;

        // Restricting to actual login attempts (logintype=login) means this code path
        // is only reachable for requests that already passed TYPO3 core's FE login
        // rate limiter in FrontendUserAuthenticator (see loginRateLimit / loginRateLimitInterval),
        // which throttles per remote address before this middleware ever runs.
        if ($givenUserName === null || $loginType !== 'login') {
            return $handler->handle($request);
        }

        /** @var FrontendTypoScript $typoscript */
        $typoscript = $request->getAttribute('frontend.typoscript');
        $pluginSettings = new ForcePasswordSettings($typoscript->getSetupArray()['plugin.']['tx_forcepasswordreset.']['settings.']);

        /** @var PageArguments $pageArguments */
        $pageArguments = $request->getAttribute('routing');

        if ($pageArguments->getPageId() === $pluginSettings->getPasswordResetPage()) {
            return $handler->handle($request);
        }

        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = $request->getAttribute('frontend.user');

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::FE_USER_TABLENAME);
        $user = $queryBuilder
            ->select('*')
            ->from(self::FE_USER_TABLENAME)
            ->where(
                $queryBuilder->expr()->eq($frontendUser->username_column, $queryBuilder->createNamedParameter($givenUserName))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($user === false || $user[Constants::PASSWORD_RESET_FIELD_NAME] === 0) {
            return $handler->handle($request);
        }

        /** @var SiteLanguage $language */
        $language = $request->getAttribute('language');

        /** @var Site $site */
        $site = $request->getAttribute('site');
        $uri = (string)$site->getRouter()->generateUri(
            $pluginSettings->getPasswordResetPage(),
            [
                '_language' => $language,
                'tx_felogin_login' => [
                    'action' => 'recovery',
                    'controller' => 'PasswordRecovery',
                ],
            ]
        );

        return $this->responseFactory->createResponse(307)->withHeader('Location', $uri);
    }
}
