<?php

declare(strict_types=1);

namespace Trueprogramming\ForcePasswordReset\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Trueprogramming\ForcePasswordReset\Settings\ForcePasswordSettings;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ResetPasswordAfterLoginRedirectMiddleware implements MiddlewareInterface
{
    private const string USERNAME_FORM_FIELD_NAME = 'user';
    private const string FE_USER_TABLENAME = 'fe_users';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly ResponseFactory $responseFactory,
        private readonly FrontendUserAuthentication $frontendUserAuthentication
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $givenUserName = $request->getParsedBody()[self::USERNAME_FORM_FIELD_NAME] ?? null;

        if ($givenUserName === null) {
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

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::FE_USER_TABLENAME);
        $user = $queryBuilder
            ->select('*')
            ->from(self::FE_USER_TABLENAME)
            ->where(
                $queryBuilder->expr()->eq($this->frontendUserAuthentication->username_column, $queryBuilder->createNamedParameter($givenUserName))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($user === false || $user['tx_forcepasswordreset_force_password_reset'] === 0) {
            return $handler->handle($request);
        }

        /** @var SiteLanguage $language */
        $language = $request->getAttribute('language');

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = $uriBuilder
            ->setTargetPageUid($pluginSettings->getPasswordResetPage())
            ->setLanguage((string)$language->getLanguageId())
            ->uriFor(
                'recovery',
                [],
                'PasswordRecovery',
                'Felogin',
                'Login'
            );

        return $this->responseFactory->createResponse(307)->withHeader('Location', $uri);
    }
}
