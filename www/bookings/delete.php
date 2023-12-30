<?php
/**
 * Delete action for the bookings application
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

include '../../includes/bookingsApp.php';

if ($input->getString('site_secret', false) !== SITE_SECRET)
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

if (!$input->exists('id'))
{
    include 'sites/header.php';
    include 'sites/not_found.php';
    include 'sites/footer.php';
    exit;
}

$bookingId = $input->getInteger('id', 0);
$booking   = $childBookingsHelper->getBookingById($bookingId);

// Check whether an valid booking has been send
if (!$bookingId || !$booking)
{
    header('Location: index.php?site_secret=' . SITE_SECRET . '&admin_secret=' . ADMIN_SECRET . '&uuid=' . $child['uuid']);
    exit;
}

if ($input->getString('admin_secret', false) === ADMIN_SECRET)
{
    $isAdmin   = true;
    $canCreate = true;
    $canEdit   = true;
    $canDelete = true;
}

if (!$canDelete)
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

$child = $childMetadataHelper->getChildByid($booking['child_id']);
$childBookingsHelper->deleteBookingById($bookingId);

header('Location: index.php?site_secret=' . SITE_SECRET . '&admin_secret=' . ADMIN_SECRET . '&uuid=' . $child['uuid']);
