<?php
/**
 * View action for the bookings application
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
$booking = $childBookingsHelper->getBookingById($bookingId);


// Check whether an valid UUID has been send
if (!$bookingId || !$booking)
{
    include 'sites/header.php';
    include 'sites/not_found.php';
    include 'sites/footer.php';
    exit;
}

if ($input->getString('admin_secret', false) === ADMIN_SECRET)
{
    $isAdmin   = true;
    $canCreate = true;
    $canEdit   = true;
    $canDelete = true;
}

$balance = $childBookingsHelper->getBookingsBalanceByChildId($booking['child_id']);
$child   = $childMetadataHelper->getChildByid($booking['child_id']);

?>
<?php include 'sites/header.php' ?>
    <div class="container">
        <h1 class="text-center">
            <?php echo SITE_TITLE_BOOKINGS_HEADING ?>
        </h1>
        <h2 class="text-center">
            Konto von <span class="badge badge-info"><?php echo $child['firstname'] . ' ' . $child['lastname']; ?></span>
        </h2>
        <h2 class="text-center">
            Dein aktueller Kontostand: <span class="badge badge-<?php echo $balance['sign'] === '+' ? 'success' : 'danger'; ?>"><?php echo $balance['sign'] . $balance['value']; ?></span>
        </h2>
        <div class="card">
            <div class="card-header">
                <h3>Buchung anzeigen: <b><?php echo $booking['reason'] ?></b></h3>
            </div>
            <table class="table">
                <tbody>
                <tr>
                    <th>Buchungsnummer:</th>
                    <td><?php echo $booking['booking_id'] ?></td>
                </tr>
                <tr>
                    <th>Empfänger:</th>
                    <td><?php echo $child['firstname'] . ' ' . $child['lastname']; ?></td>
                </tr>
                <tr>
                    <th>Verwendungszweck:</th>
                    <td><?php echo $booking['reason'] ?></td>
                </tr>
                <tr>
                    <th>Wert:</th>
                    <td><span class="badge badge-<?php echo $booking['value_sign'] === '+' ? 'success' : 'danger'; ?>"><?php echo $booking['value_sign'] . $booking['value']; ?></span></td>
                </tr>
                <tr>
                    <th>Datum:</th>
                    <td><?php echo $booking['date'] ?></td>
                </tr>
                <tr>
                    <th>Uhrzeit:</th>
                    <td><?php echo $booking['time'] ?></td>
                </tr>
                <tr>
                    <th>Icon:</th>
                    <td><i class="fa-solid fa-<?php echo $booking['icon'] ?>"></i></td>
                </tr>
                </tbody>
            </table>
            <div class="card-body">
                <a class="btn btn-info" href="index.php?site_secret=<?php echo SITE_SECRET ?><?php echo $isAdmin === true ? '&admin_secret=' . ADMIN_SECRET : '' ?>&uuid=<?php echo $child['uuid'] ?>">Zurück</a>
                <?php if($canEdit) : ?>
                   <a class="btn btn-secondary" href="edit.php?site_secret=<?php echo SITE_SECRET ?>&admin_secret=<?php echo ADMIN_SECRET ?>&id=<?php echo $booking['booking_id'] ?>">Bearbeiten</a>
                <?php endif; ?>
                <?php if($canDelete) : ?>
                    <form class="inline-block" method="POST" action="delete.php">
                        <input type="hidden" name="site_secret" value="<?php echo SITE_SECRET ?>">
                        <input type="hidden" name="admin_secret" value="<?php echo ADMIN_SECRET ?>">
                        <input type="hidden" name="id" value="<?php echo $booking['booking_id'] ?>">
                        <button class="btn btn-danger">Löschen</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php include 'sites/footer.php' ?>
