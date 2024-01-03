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
    <?php if ($canCreate || $isLoggedIn) : ?>
        <h4 class="d-none d-md-table-cell d-lg-table-cell d-xl-table-cell">Aktionen</h4>
        <p>
            <?php if ($canCreate) : ?>
                <a class="btn btn-primary btn-lg" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=custom">Neue Buchung</a>
                <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=present">Kind anwesend</a>
                <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=absent">Kind abwesend</a>
                <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=breakfast">Frühstück</a>
                <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=lunch">Mittagessen</a>
                <a class="btn btn-success" href="create.php?site_secret=<?php echo SITE_SECRET ?>&child_id=<?php echo $child['child_id'] ?>&type=dinner">Abendessen</a>
            <?php endif ?>
            <?php if ($isLoggedIn) : ?>
                <a class="btn btn-danger float-right" href="../admin/logout.php?site_secret=<?php echo SITE_SECRET ?>&admin_secret=<?php echo ADMIN_SECRET ?>&uuid=<?php echo $child['uuid'] ?>&return=bookings.index.uuid">Abmelden</a>
            <?php endif ?>
        </p>
    <?php endif ?>
    <table class="table">
        <thead>
            <tr>
                <th class="d-none d-md-table-cell d-lg-table-cell d-xl-table-cell">Datum</th>
                <th class="d-none d-md-table-cell d-lg-table-cell d-xl-table-cell">Uhrzeit</th>
                <th class="d-block d-sm-none">Zeit</th>
                <th>Beschreibung</th>
                <th>Wert</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking) : ?>
                <tr class="table-<?php echo $booking['value_sign'] === '+' ? 'success' : 'danger'; ?>">
                    <td class="d-none d-md-table-cell d-lg-table-cell d-xl-table-cell"><?php echo $booking['date'] ?></td>
                    <td class="d-none d-md-table-cell d-lg-table-cell d-xl-table-cell"><?php echo $booking['time'] ?></td>
                    <td class="d-block d-sm-none"><?php echo $booking['date'] . ' ' . $booking['time'] ?></td>
                    <td><i class="fa-solid fa-<?php echo $booking['icon'] ?>"></i> <?php echo $booking['reason'] ?></td>
                    <td><span class="badge badge-<?php echo $booking['value_sign'] === '+' ? 'success' : 'danger'; ?>"><?php echo $booking['value_sign'] . '' . $booking['value'] ?></span></td>
                    <td>
                        <a href="view.php?site_secret=<?php echo SITE_SECRET ?>&id=<?php echo $booking['booking_id'] ?>" class="btn btn-sm btn-info">Anschauen</a>
                        <?php if ($canUpdate) : ?>
                            <a href="update.php?site_secret=<?php echo SITE_SECRET ?>&id=<?php echo $booking['booking_id'] ?>" class="btn btn-sm btn-secondary">Bearbeiten</a>
                        <?php endif; ?>
                        <?php if ($canDelete) : ?>
                            <form method="POST" action="delete.php">
                                <input type="hidden" name="site_secret" value="<?php echo SITE_SECRET ?>">
                                <input type="hidden" name="id" value="<?php echo $booking['booking_id'] ?>">
                                <button class="btn btn-sm btn-danger">Löschen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<?php include 'sites/footer.php' ?>
