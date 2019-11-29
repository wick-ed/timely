<?php

/**
 * \Wicked\Timely\Storage\StorageFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Storage;

use Wicked\Timely\Entities\Booking;

/**
 * Storage interface
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
interface StorageInterface
{

    /**
     * Getter for the log file path
     *
     * @return string
     */
    public function getLogFilePath();

    /**
     * Stores a single booking
     *
     * @param \Wicked\Timely\Entities\Booking $booking The booking to store
     *
     * @return void
     */
    public function store(Booking $booking);

    /**
     * Stores a single booking
     *
     * @param \Wicked\Timely\Entities\Booking $booking The booking to store
     *
     * @return void
     */
    public function storePush(Booking $booking);

    /**
     * Retrieves retrieves the last booking from the storage
     *
     * @param boolean $includeMetaTickets Whether or not the retrieved booking can be a meta ticket
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public function retrieveLast($includeMetaTickets = false);

    /**
     * Retrieves one or several bookings from the storage. Bookings can be filtered by pattern,
     * date, etc.
     *
     * @param null|string  $pattern   A pattern to filter ticket IDs for. Defaults to NULL
     * @param null|integer $toDate    Date up to which bookings will be returned. Defaults to NULL
     * @param null|integer $fromDate  Date from which on bookings will be returned. Defaults to NULL
     * @param null|integer $limit     Number of non-meta bookings the retrieval is limited to. Defaults to NULL
     * @param boolean      $dontClip  Whether or not the retrieved bookings should be clipped where appropriate. Defaults to FALSE
     * @param boolean      $countMeta Whether or not meta tickets will be included in the counter which is used for our limit
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function retrieve($pattern = null, $toDate = null, $fromDate = null, $limit = null, $dontClip = false, $countMeta = false);
}
