<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$additionalColumns = [
    'tx_forcepasswordreset_force_password_reset' => [
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
    'tx_forcepasswordreset_force_password_reset'
);
ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--palette--;Force password reset;forcepasswordreset',
    '',
    'after:lastlogin'
);
