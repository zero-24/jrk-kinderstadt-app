<?php
/**
 * login action for the admin application
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

include '../../includes/adminApp.php';

if ($input->getString('site_secret', false) !== SITE_SECRET)
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

// Default set of permissions
$canCreate = false;
$canRead   = true;
$canUpdate = false;
$canDelete = false;
$isAdmin   = false;

if ($input->getString('admin_secret', false) === ADMIN_SECRET)
{
    $canCreate = true;
    $canRead   = true;
    $canUpdate = true;
    $canDelete = true;
    $isAdmin   = true;
}

$sessionValue = $input->cookie->get(LOGIN_COOKIE_NAME, null);

// If there's no cookie value, manually set it
if ($sessionValue === null)
{
    $sessionValue = $sessionHelper->generateNewUUIDv4();
}

// Check whether the current session exists
$currentSession = $sessionHelper->getSessionById($sessionValue);

// In this case the current session is still valid
if ($currentSession && $currentSession['expire'] > time())
{
    // Get the permissions from the current session
    $canCreate = $currentSession['canCreate'];
    $canRead   = $currentSession['canRead'];
    $canUpdate = $currentSession['canUpdate'];
    $canDelete = $currentSession['canDelete'];
    $isAdmin   = $currentSession['isAdmin'];

    $currentSession['expire'] = time() + 43200; // 12 Stunden gültig

    $sessionHelper->editSession($currentSession, $currentSession['session_id']);

    $cookieOptions = [
        'expires'  => $currentSession['expire'],
        'path'     => COOKIE_PATH,
        'domain'   => COOKIE_DOMAIN,
        'secure'   => COOKIE_SECURE,
        'httponly' => true,
        'samesite' => 'Strict',
    ];

    // Write session
    $input->cookie->set(LOGIN_COOKIE_NAME, $currentSession['session_id'], $cookieOptions);
}
else
{
    // Create new session
    $sessionUUID = $sessionHelper->generateNewUUIDv4();

    // Write Session
    $newExpireTime = time() + COOKIE_EXPIRES; // 12 Stunden gültig

    $newSession = [
        'session_id' => $sessionUUID,
        'canCreate'  => $canCreate,
        'canRead'    => $canRead,
        'canUpdate'  => $canUpdate,
        'canDelete'  => $canDelete,
        'isAdmin'    => $isAdmin,
        'expire'     => $newExpireTime,
    ];

    $sessionHelper->createSession($newSession);

    $cookieOptions = [
        'expires'  => $newExpireTime,
        'path'     => COOKIE_PATH,
        'domain'   => COOKIE_DOMAIN,
        'secure'   => COOKIE_SECURE,
        'httponly' => true,
        'samesite' => 'Strict',
    ];

    $input->cookie->set(LOGIN_COOKIE_NAME, $sessionUUID, $cookieOptions);
}

// Clean up outdated sessions
$sessionHelper->cleanUpSessions();

$return = $input->getString('return', false);

if ($return && in_array($return, $protectedActions))
{
    $returnParts = explode('.', $return);

    header('Location: ../' . $returnParts[0] . '/' . $returnParts[1] . '.php?site_secret=' . SITE_SECRET);
    exit;
}

// Redirect to the public page app
header('Location: ../public/loggedIn.php');
exit;
