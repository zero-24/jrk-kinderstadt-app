<?php
/**
 * Main entry point for the jrk-kinderstadt-app application
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

include '../includes/indexApp.php';

// Check whether the correct secret has been set
if ($input->getString('site_secret', false) !== SITE_SECRET)
{
    // Redirect to the public app
    header('Location: public/index.php');
    exit;
}

$uuid = $input->getString('uuid', false);

// Check whether an valid UUID has been send
if ($uuid && $childMetadataHelper->isValidUUID($uuid))
{
    header('Location: bookings/index.php?site_secret=' . SITE_SECRET . '&uuid=' . $uuid);
    exit;
}

// Redirect to the public app
header('Location: public/index.php');
