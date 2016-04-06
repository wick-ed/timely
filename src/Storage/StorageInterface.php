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
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Storage;

use Wicked\Timely\Entities\Booking;

/**
 * Storage interface
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
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
     */
    public function store(Booking $booking);

    /**
     * Retrieves one or several bookings from the storage. Bookings can be filtered by pattern,
     * date, etc.
     *
     * @param null|string  $pattern  A pattern to filter ticket IDs for
     * @param null|integer $toDate   Date up to which bookings will be returned
     * @param null|integer $fromDate Date from which on bookings will be returned
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function retrieve($pattern = null, $toDate = null, $fromDate = null);
}
