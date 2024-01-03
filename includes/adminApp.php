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
use zero24\Helper\SessionHelper;

$input = new Input\Input;

$sessionHelper = new SessionHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'session',
]);

$allowedReturns = [
    'bookings.index.uuid',
];

