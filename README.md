# force password reset
    Author: Michael Semle
    E-Mail: git@mikeproduction.de
    repo:   https://github.com/true-programming/force-password-reset

This extension provides logic to force fe users to reset their password.

## Usage
* Place a login form on your site content
* Define a password reset page where to redirect in typoscript. Important: This should not be the same site as the login form.
* Place another login form on the password reset page.

## Features
* Command to set all fe_users to reset their password.

## When do I need this extension?
If it is necessary to force all users to change their password. For example for security reasons.

## How to install this extension?

You can set this up via composer (`composer req trueprogramming/force-password-reset`).

## Requirements

* TYPO3 v12 + v13.

## License

The extension is licensed under GPL v2+, same as the TYPO3 Core.

For details see the LICENSE file in this repository.
