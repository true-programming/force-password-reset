<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$additionalColumns = [
    \Trueprogramming\ForcePasswordReset\Constants::PASSWORD_RESET_FIELD_NAME => [
        'label' => 'Needs to reset password at next login',
        'exclude' => true,
        'config' => [
            'type' => 'check',
            'readOnly' => true,
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('fe_users', $additionalColumns);
ExtensionManagementUtility::addFieldsToPalette(
    'fe_users',
    'forcepasswordreset',
    \Trueprogramming\ForcePasswordReset\Constants::PASSWORD_RESET_FIELD_NAME
);
ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--palette--;Force password reset;forcepasswordreset',
    '',
    'after:lastlogin'
);
