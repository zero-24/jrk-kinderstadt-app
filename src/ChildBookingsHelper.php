<?php
/**
 * ChildBookingsHelper class
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace zero24\Helper;

use zero24\Helper\FileHelper;

/**
 * Class for ChildBookingsHelper
 *
 * @since  1.0
 */
class ChildBookingsHelper
{
    /**
     * The filename with the data files
     *
     * @var    string
     * @since  1.0
     */
    private $fileName = 'child_bookings';

    /**
     * The FileHelper object pointing to the data folder
     *
     * @var    FileHelper
     * @since  1.0
     */
    private $fileHelper;

    /**
     * Constructor.
     *
     * @param   array  $options  Options to init the connection
     *
     * @since   1.0
     */
    public function __construct($options)
    {
        $this->fileHelper = new FileHelper([
            'dataFolder' => $options['dataFolder'],
        ]);

        $this->fileName = $options['fileName'];
    }

    /**
     * Get all Tracker Data from the text mapping file
     *
     * @return  string  Decoded JSON object with all tracker information
     *
     * @since   1.0
     */
    public function getBookings()
    {
        return $this->fileHelper->readJsonFile($this->fileName);
    }

    /**
     * Get next booking id
     *
     * @return  integer  Next free booking ID
     *
     * @since   1.0
     */
    public function getNextBookingId(): int
    {
        $bookings = $this->getBookings();

        foreach ($bookings as $booking)
        {
            $lastId = $booking['booking_id'];
        }

        // Increase the last point Id by one
        $lastId++;

        return (int) $lastId;
    }

    /**
     * Get one specific booking by ID
     *
     * @param   string  $bookingId  booking ID
     *
     * @return  object|false  Decoded JSON object with the requested booking information or false
     *
     * @since   1.0
     */
    public function getBookingById($bookingId)
    {
        $bookings = $this->getBookings();

        foreach ($bookings as $booking)
        {
            if ((int) $booking['booking_id'] === $bookingId)
            {
                return $booking;
            }
        }

        return false;
    }

    /**
     * Get all bookings by child ID
     *
     * @param   string  $childId  child ID
     *
     * @return  array  Decoded JSON object with the requested booking information or false
     *
     * @since   1.0
     */
    public function getBookingsByChildId($childId): array
    {
        $bookings = $this->getBookings();
        $childBookings = [];

        foreach ($bookings as $booking)
        {
            if ($booking['child_id'] === $childId)
            {
                $childBookings[] = $booking;
            }
        }

        return $childBookings;
    }

    /**
     * Get booking balance by child id
     *
     * @param   string  $childId  child ID
     *
     * @return  array  Array with the current balance and the balance sign
     *
     * @since   1.0
     */
    public function getBookingsBalanceByChildId($childId): array
    {
        $childBookings = $this->getBookingsByChildId($childId);
        $balance = ['sign' => '+', 'value' => 0];

        foreach ($childBookings as $booking)
        {
            if ($booking['value_sign'] === '+')
            {
                $balance['value'] = (int) $balance['value'] + (int) $booking['value'];
                continue;
            }

            if ($booking['value_sign'] === '-')
            {
                $balance['value'] = (int) $balance['value'] - (int) $booking['value'];
                continue;
            }
        }

        if ((integer) $balance['value'] < 0)
        {
            $balance['sign'] = '-';
            $balance['value'] = substr($balance['value'], 1);
        }

        return $balance;
    }

    /**
     * Add a new booking to the json file
     *
     * @param   array  $data  Booking data posted to the app
     *
     * @return  array  Booking data posted to the app
     *
     * @since   1.0
     */
    public function createBooking($data): array
    {
        $bookings   = $this->getBookings();
        $bookings[] = $data;

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $bookings,
                JSON_PRETTY_PRINT
            )
        );

        return $data;
    }

    /**
     * Edit an existing booking from the json file
     *
     * @param   array   $data       New booking data posted to the app
     * @param   string  $bookingId  Booking ID that should be edited
     *
     * @return  array  Booking data posted to the app
     *
     * @since   1.0
     */
    public function editBooking($data, $bookingId): array
    {
        $editBooking = [];
        $bookings  = $this->getBookings();

        foreach ($bookings as $i => $booking)
        {
            if ((int) $booking['booking_id'] === $bookingId)
            {
                $bookings[$i] = $editBooking = array_merge($booking, $data);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $bookings,
                JSON_PRETTY_PRINT
            )
        );

        return $editBooking;
    }

    /**
     * Delete an existing booking from the json file
     *
     * @param   integer  booking ID to be deleted
     *
     * @return  void
     *
     * @since   1.0
     */
    public function deleteBookingById($bookingId): void
    {
        $bookings = $this->getBookings();

        foreach ($bookings as $i => $booking)
        {
            if ((int) $booking['booking_id'] === $bookingId)
            {
                array_splice($bookings, $i, 1);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $bookings,
                JSON_PRETTY_PRINT
            )
        );
    }

    /**
     * Validate the data passed to the app
     *
     * @param   array   &$booking  The booking data to be validated (referenced)
     * @param   array   &$errors   The errors collected while validating (referenced)
     * @param   string  $type      String wether we are in 'edit' or 'create' mode
     *
     * @return  bool
     *
     * @since   1.0
     */
    public function validateBooking(&$booking, &$errors, $type): bool
    {
        $isValid = true;

        // Start of validation
        if (!$booking['booking_id'] || ($type === 'create' && $this->getBookingById($booking['booking_id'])))
        {
            $isValid = false;
            $errors['booking_id'] = 'Booking ID is mandatory and has to be unique';
        }

        // Todo: Validation for the other fields
        return $isValid;
    }

    /**
     * Get already used Icons
     *
     * @return  array  List all used and proposed Icons
     *
     * @since   1.0
     */
    public function getBookingsIcons(): array
    {
        $bookings      = $this->getBookings();
        $bookingsIcons = ICON_ARRAY_SUGGESTION;

        foreach ($bookings as $booking)
        {
            if (!isset($bookingsIcons[$booking['icon']]))
            {
                $bookingsIcons[$booking['icon']] = $booking['icon'];
            }
        }

        return array_unique($bookingsIcons);
    }
}
