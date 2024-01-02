<?php
/**
 * Create action for the bookings application
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

if (!$canCreate)
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

$childId = $input->getString('child_id');
$child   = $childMetadataHelper->getChildById($childId);

if (!$childId || !$child)
{
    header('Location: ../public/index.php');
    exit;
}

$booking = [
    'booking_id' => $childBookingsHelper->getNextBookingId(),
    'type'       => 'custom',
    'child_id'   => $childId,
    'icon'       => 'bag-shopping',
    'reason'     => '',
    'value'      => '',
    'value_sign' => '+',
    'date'       => date("Y-m-d"),
    'time'       => date("H:i:s"),
];

$errors = [
    'booking_id' => '',
    'child_id'   => '',
    'icon'       => '',
    'reason'     => '',
    'value'      => '',
    'value_sign' => '',
    'date'       => '',
    'time'       => '',
];

$bookingType = $input->getString('type', 'custom');

// This booking types are booked directly
if (in_array($bookingType, ['present', 'absent', 'breakfast', 'lunch', 'dinner']))
{
    $booking = [
        'booking_id' => $childBookingsHelper->getNextBookingId(),
        'type'       => $bookingType,
        'child_id'   => $childId,
        'icon'       => '',
        'reason'     => '',
        'value'      => '0',
        'value_sign' => '',
        'date'       => date("Y-m-d"),
        'time'       => date("H:i:s"),
    ];

    switch ($bookingType)
    {
        case 'present':
            $booking['icon'] = 'check';
            $booking['reason'] = 'Kind ist anwesend';
            $booking['value_sign'] = '+';
            break;

        case 'absent':
            $booking['icon'] = 'ban';
            $booking['reason'] = 'Kind ist nicht anwesend';
            $booking['value_sign'] = '-';
            break;

        case 'breakfast':
            $booking['icon'] = 'egg';
            $booking['reason'] = 'Frühstück am ' . date("d.m.Y");
            $booking['value'] = '20';
            $booking['value_sign'] = '-';
            break;

        case 'lunch':
            $booking['icon'] = 'carrot';
            $booking['reason'] = 'Mittagessen am ' . date("d.m.Y");
            $booking['value'] = '20';
            $booking['value_sign'] = '-';
            break;

        case 'dinner':
            $booking['icon'] = 'bread-slice';
            $booking['reason'] = 'Abendessen am ' . date("d.m.Y");
            $booking['value'] = '20';
            $booking['value_sign'] = '-';
            break;
    }

    $childBookingsHelper->createBooking($booking);
    $child = $childMetadataHelper->getChildById($booking['child_id']);

    header('Location: index.php?site_secret=' . SITE_SECRET . '&uuid=' . $child['uuid']);
}

$isValid = true;

if ($input->getMethod() === 'POST')
{
    foreach ($booking as $key => $value)
    {
        $booking[$key] = $input->getString($key);
    }

    // Check whether the data is valid $tracker and $errors are passed by reference
    $isValid = $childBookingsHelper->validateBooking($booking, $errors, 'create');

    if ($isValid)
    {
        $childBookingsHelper->createBooking($booking);

        header('Location: index.php?site_secret=' . SITE_SECRET . '&uuid=' . $child['uuid']);
    }
}

?>
<?php include 'sites/header.php' ?>
<?php include 'sites/form.php' ?>
<?php include 'sites/footer.php' ?>
