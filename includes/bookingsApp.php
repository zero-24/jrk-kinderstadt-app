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

$input = new Input\Input;

$childBookingsHelper = new ChildBookingsHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'child_bookings',
]);

$childMetadataHelper = new ChildMetadataHelper([
    'dataFolder' => ROOT_PATH . '/data',
    'fileName' => 'child_metadata',
]);

// Initial Permission settings
$isAdmin   = false;
$canCreate = false;
$canEdit   = false;
$canDelete = false;

