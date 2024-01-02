<?php
/**
 * bookingsApp include
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

include '../../etc/constants.php';

// Ensure we've initialized Composer
if (!file_exists(ROOT_PATH . '/vendor/autoload.php'))
{
    exit(1);
}

require ROOT_PATH . '/vendor/autoload.php';

use Joomla\Input;
use zero24\Helper\ChildBookingsHelper;
use zero24\Helper\ChildMetadataHelper;
use zero24\Helper\SessionHelper;

$input = new Input\Input;

$childBookingsHelper = new ChildBookingsHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'child_bookings',
]);

$childMetadataHelper = new ChildMetadataHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'child_metadata',
]);

$sessionHelper = new SessionHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'session',
]);

// Initial Permission settings
$canCreate  = false;
$canRead    = false;
$canUpdate  = false;
$canDelete  = false;
$isAdmin    = false;
$isLoggedIn = false;

$sessionValue = $input->cookie->get('jrk-kinderstadt-app-session', false);

if ($sessionValue)
{
    // Check whether the current session exists
    $currentSession = $sessionHelper->getSessionById($sessionValue);

    // In this case the current session is still valid
    if ($currentSession && $currentSession['expire'] > time())
    {
        // Get the permissions from the current session
        $canCreate  = $currentSession['canCreate'];
        $canRead    = $currentSession['canRead'];
        $canUpdate  = $currentSession['canUpdate'];
        $canDelete  = $currentSession['canDelete'];
        $isAdmin    = $currentSession['isAdmin'];
        $isLoggedIn = true;
    }
}

