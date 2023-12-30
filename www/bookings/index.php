<?php
/**
 * Main entry point for the bookings application
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

$childUUID = $input->getString('uuid', false);

// Check whether an valid UUID has been send
if (!$childUUID || !$childMetadataHelper->isValidUUID($childUUID))
{
    // Redirect to the public page app
    header('Location: ../public/index.php');
    exit;
}

if ($input->getString('admin_secret', false) === ADMIN_SECRET)
{
    $isAdmin   = true;
    $canCreate = true;
    $canEdit   = true;
    $canDelete = true;
}

$child    = $childMetadataHelper->getChildByUUID($childUUID);
$bookings = $childBookingsHelper->getBookingsByChildId($child['child_id']);
$balance  = $childBookingsHelper->getBookingsBalanceByChildId($child['child_id']);

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
    <table class="table">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Uhrzeit</th>
                <th>Verwendungszweck</th>
                <th>Wert</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking) : ?>
                <tr class="table-<?php echo $booking['value_sign'] === '+' ? 'success' : 'danger'; ?>">
                    <td><?php echo $booking['date'] ?></td>
                    <td><?php echo $booking['time'] ?></td>
                    <td><i class="fa-solid fa-<?php echo $booking['icon'] ?>"></i> <?php echo $booking['reason'] ?></td>
                    <td><?php echo $booking['value_sign'] . '' . $booking['value'] ?></td>
                    <td>
                        <a href="view.php?site_secret=<?php echo SITE_SECRET ?><?php echo $isAdmin === true ? '&admin_secret=' . ADMIN_SECRET : ''; ?>&id=<?php echo $booking['booking_id'] ?>" class="btn btn-sm btn-info">Anschauen</a>
                        <?php if ($canEdit) : ?>
                            <a href="edit.php?site_secret=<?php echo SITE_SECRET ?>&admin_secret=<?php echo ADMIN_SECRET ?>&id=<?php echo $booking['booking_id'] ?>" class="btn btn-sm btn-secondary">Bearbeiten</a>
                        <?php endif; ?>
                        <?php if ($canDelete) : ?>
                            <form method="POST" action="delete.php">
                                <input type="hidden" name="site_secret" value="<?php echo SITE_SECRET ?>">
                                <input type="hidden" name="admin_secret" value="<?php echo ADMIN_SECRET ?>">
                                <input type="hidden" name="id" value="<?php echo $booking['booking_id'] ?>">
                                <button class="btn btn-sm btn-danger">Löschen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php if($canCreate) : ?>
        <p>
            <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&admin_secret=<?php echo ADMIN_SECRET ?>&child_id=<?php echo $booking['child_id'] ?>">Neue Buchung</a>
        </p>
    <?php endif; ?>
</div>
<?php include 'sites/footer.php' ?>
