<?php

return [
    'frontend' => [
        'trueprogramming/forcepasswordreset/reset-password-after-login-redirect' => [
            'target' => \Trueprogramming\ForcePasswordReset\Middleware\ResetPasswordAfterLoginRedirectMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
