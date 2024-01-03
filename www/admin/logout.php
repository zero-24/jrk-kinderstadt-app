<?php
/**
 * logout action for the admin application
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

if ($input->getString('admin_secret', false) !== ADMIN_SECRET)
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

$sessionValue = $input->cookie->get('jrk-kinderstadt-app-session', null);

// If there's no cookie value, manually set it
if ($sessionValue !== null)
{
    $sessionHelper->deleteSession($sessionValue);
    $sessionValue = $input->cookie->set('jrk-kinderstadt-app-session', null, ['expires' => time() - 10000]);
}

// Clean up outdated sessions
$sessionHelper->cleanUpSessions();

$returnValue = $input->getString('return', false);

if ($returnValue && in_array($returnValue, $allowedReturns))
{
    $returnParts = explode('.', $returnValue);

    if (count($returnParts) === 3)
    {
        header('Location: ../' . $returnParts[0] . '/' . $returnParts[1] . '.php?site_secret=' . SITE_SECRET . '&'. $returnParts[2] . '=' . $input->getString($returnParts[2], ''));
        exit;
    }

    header('Location: ../' . $returnParts[0] . '/' . $returnParts[1] . '.php?site_secret=' . SITE_SECRET);
    exit;
}

// Redirect to the public page app
header('Location: ../public/index.php');
exit;
