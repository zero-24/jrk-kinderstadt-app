<?php $child = !empty($booking['child_id']) ? $childMetadataHelper->getChildById($booking['child_id']) : $childMetadataHelper->getChildById($input->getString('child_id')); ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>
                <?php if ($booking['booking_id']): ?>
                    Buchung bearbeiten <b><?php echo $booking['reason'] ?></b>
                <?php else : ?>
                    Neue Buchung erstellen
                <?php endif ?>
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="">
                <div class="form-group">
                    <label>Buchungs ID</label>
                    <?php if ($booking['booking_id']) : ?>
                        <input name="booking_id" type="text" readonly value="<?php echo $booking['booking_id'] ?>" class="form-control <?php echo $errors['booking_id'] ? 'is-invalid' : '' ?>">
                    <?php else : ?>
                        <input name="booking_id" type="text" readonly value="<?php echo $childBookingsHelper->getNextBookingId() ?>" class="form-control <?php echo $errors['booking_id'] ? 'is-invalid' : '' ?>">
                    <?php endif ?>
                    <div class="invalid-feedback">
                        <?php echo $errors['booking_id'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Empfänger</label>
                    <div class="form-group row">
                        <div class="col">
                            <?php if ($booking['booking_id']) : ?>
                                <input name="child_id" type="text" readonly value="<?php echo $booking['child_id'] ?>" class="form-control <?php echo $errors['child_id'] ? 'is-invalid' : '' ?>">
                            <?php else : ?>
                                <input name="child_id" type="text" readonly value="<?php echo $input->getString('child_id') ?>" class="form-control <?php echo $errors['child_id'] ? 'is-invalid' : '' ?>" />
                            <?php endif ?>
                        </div>
                        <div class="col">
                            <?php  ?>
                            <input name="child_id_title" type="text" readonly value="<?php echo $child ? $child['firstname'] . ' ' . $child['lastname'] : '' ?>" class="form-control <?php echo $errors['child_id'] ? 'is-invalid' : '' ?>" />
                        </div>
                    </div>
                    <div class="invalid-feedback">
                        <?php echo $errors['child_id'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Verwendungszweck</label>
                    <input name="reason" type="text" value="<?php echo $booking['reason'] ?>" class="form-control <?php echo $errors['reason'] ? 'is-invalid' : '' ?>">
                    <div class="invalid-feedback">
                        <?php echo $errors['reason'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Wert</label>
                    <input name="value" type="number" value="<?php echo $booking['value'] ?>" class="form-control <?php echo $errors['value'] ? 'is-invalid' : '' ?>">
                    <div class="invalid-feedback">
                        <?php echo $errors['value'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Vorzeichen</label>
                    <div class="form-group row">
                        <div class="col">
                            <input name="value_sign" type="text" value="<?php echo $booking['value_sign'] ?>" list="signDataList" class="form-control <?php echo $errors['value_sign'] ? 'is-invalid' : '' ?>" />
                                <datalist id="signDataList">
                                    <option value="+">Plus</option>
                                    <option value="-">Minus</option>
                                </datalist>
                            </div>
                            <div class="col">
                                <?php $value = isset($booking['value_sign']) ? ($booking['value_sign'] === '+' ? 'Plus' : '') : ($booking['value_sign'] === '-' ? 'Minus' : ''); ?>
                                <input name="value_sign_title" type="text" readonly value="<?php echo $value ?>" class="form-control <?php echo $errors['value_sign'] ? 'is-invalid' : '' ?>" />
                            </div>
                        <div class="invalid-feedback">
                            <?php echo $errors['value_sign'] ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Datum</label>
                    <input name="date" type="date" readonly value="<?php echo $booking['date'] ? $booking['date'] : date("Y-m-d") ?>" class="form-control <?php echo $errors['date'] ? 'is-invalid' : '' ?>">
                    <div class="invalid-feedback">
                        <?php echo $errors['date'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Zeit</label>
                    <input name="time" type="time" readonly value="<?php echo $booking['time'] ? $booking['time'] : date("H:i:s") ?>" class="form-control <?php echo $errors['time'] ? 'is-invalid' : '' ?>">
                    <div class="invalid-feedback">
                        <?php echo $errors['time'] ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Icon</label>
                    <?php $metadataIcons = $childBookingsHelper->getBookingsIcons() ?>
                    <div class="form-group row">
                        <div class="col">
                            <input name="icon" type="text" value="<?php echo $booking['icon'] ?>" list="iconDataList" class="form-control <?php echo $errors['icon'] ? 'is-invalid' : '' ?>" />
                            <datalist id="iconDataList">
                                <?php foreach ($metadataIcons as $metadataIcon => $metadataIconTitle) : ?>
                                    <?php echo '<option value="' . $metadataIcon . '"' . '>' . $metadataIconTitle . '</option>' ?>
                                <?php endforeach ?>
                            </datalist>
                        </div>
                        <div class="col">
                            <input name="icon_title" type="text" readonly value="<?php echo isset($metadataIcons[$booking['icon']]) ? $metadataIcons[$booking['icon']] : '' ?>" class="form-control <?php echo $errors['icon'] ? 'is-invalid' : '' ?>" />
                        </div>
                        <div class="invalid-feedback">
                            <?php echo $errors['icon'] ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="site_secret" value="<?php echo SITE_SECRET ?>">
                <input type="hidden" name="type" value="<?php echo $booking['type'] ? $booking['type'] : 'custom' ?>"">
                <button type="submit" class="btn btn-success">Speichern</button>
                <a class="btn btn-info" href="index.php?site_secret=<?php echo SITE_SECRET ?>&uuid=<?php echo $child['uuid'] ?>">Zurück</a>
            </form>
        </div>
    </div>
</div>
