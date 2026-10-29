<?php

return [
    'frontend' => [
        'trueprogramming/forcepasswordreset/reset-password-after-login-redirect' => [
            'target' => \Trueprogramming\ForcePasswordReset\Middleware\ResetPasswordAfterLoginRedirectMiddleware::class,
            // Needs the "frontend.user", "routing" and "frontend.typoscript" request attributes
            'after' => [
                'typo3/cms-frontend/authentication',
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
    ],
];
