<?php

/**
 * \Wicked\Timely\Storage\File
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
use Wicked\Timely\Entities\Clipping;
use Wicked\Timely\Formatter\FormatterFactory;
use Wicked\Timely\Entities\BookingFactory;

/**
 * File storage
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class File implements StorageInterface
{

    /**
     * Default character for a line break in the format
     *
     * @var string LINE_BREAK
     */
    const LINE_BREAK = ';';

    /**
     * Default character sequence for segment separation
     *
     * @var string SEPARATOR
     */
    const SEPARATOR = ' | ';

    /**
     * Name of the log file
     *
     * @var string DATA_NAME
     */
    const DATA_NAME = 'timely-log.txt';

    /**
     * Path to the log file
     *
     * @var string $logFilePath
     */
    protected $logFilePath;

    /**
     * Default constructor
     */
    public function __construct()
    {
        // calculate the default file path
        $this->logFilePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . self::DATA_NAME;
        // check if the file exists, if not create it
        if (!is_file($this->logFilePath)) {
            touch($this->logFilePath);
        }
    }

    /**
     * Getter for the log file path
     *
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * Stores a single booking
     *
     * @param \Wicked\Timely\Entities\Booking $booking The booking to store
     *
     * @return void
     */
    public function store(Booking $booking)
    {
        // get the formatter and convert to string
        $formatter = FormatterFactory::getFormatter();
        $bookString = $formatter->toString($booking);

        // write the new booking to the beginning of the file
        $path = $this->getLogFilePath();
        file_put_contents($path, $bookString . file_get_contents($path));
    }

    /**
     * Get the content of the files storage
     *
     * @return string
     */
    protected function getStorageContent()
    {
        return file_get_contents($this->getLogFilePath());
    }

    /**
     * Retrieves retrieves the last booking from the storage
     *
     * @param boolean $includeMetaTickets Whether or not the retrieved booking can be a meta ticket
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public function retrieveLast($includeMetaTickets = false) {
        $tmp = $this->retrieve(null, null, null, 1, true, $includeMetaTickets);
        return array_pop($tmp);
    }

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
    public function retrieve($pattern = null, $toDate = null, $fromDate = null, $limit = null, $dontClip = false, $countMeta = false)
    {
        // test if we got a pattern, if not match against all
        if (is_null($pattern)) {
            $pattern = '*';
        }
        // test if we got some dates to filter by
        if (is_null($toDate)) {
            $toDate = 9999999999;
        }
        if (is_null($fromDate)) {
            $fromDate = 0;
        }

        // get the raw entries
        $rawData = $this->getStorageContent();
        $rawEntries = explode(self::LINE_BREAK, rtrim($rawData, self::LINE_BREAK));

        $entries = array();

        // iterate them and generate the entities
        $bookingKey = null;
        $bookingCount = 0;
        foreach ($rawEntries as $key => $rawEntry) {
            // get the potential entry and filter them by ticket ID
            $entry = explode(self::SEPARATOR, trim($rawEntry, ' |'));
            $timestamp = strtotime($entry[0]);
            if (isset($entry[1]) &&
                (fnmatch($pattern, $entry[1]) || isset(BookingFactory::getAllMetaTicketIds()[$entry[1]])) &&
                $timestamp > $fromDate && $timestamp < $toDate
            ) {
                // collect the actual booking
                $comment = isset($entry[2]) ? $entry[2] : '';
                $booking = BookingFactory::getBooking($comment, $entry[1], $entry[0]);
                $entries[] = $booking;

                // increase the booking counter
                if (!$booking->isMetaBooking() || $countMeta) {
                    $bookingCount ++;
                }

                // if clipping is not omitted we will add the rear clipping to our collection.
                // We do it here to make sure we get the correct day
                if (count($entries) === 1 && !$dontClip) {
                    // test if the last booking is from the today, if not we have to clip at the end of the last booked day
                    $bookingTime = new \DateTime($booking->getTime());
                    $now = new \DateTime();
                    $interval = $bookingTime->diff($now);
                    if ($interval->days === 0) {
                        $entries[] = BookingFactory::getBooking('', Clipping::CLIPPING_TAG_REAR);
                    } else {
                        $entries[] = BookingFactory::getBooking('', Clipping::CLIPPING_TAG_REAR, date('Y-m-d', strtotime($booking->getTime()) + 24 * 60 * 60));
                    }
                    // reverse entries array to let it start with our clipping again
                    $entries = array_reverse($entries);
                }

                // collect keys we found something for, for later re-use
                $bookingKey = $key;

                // break if we got as much bookings as our limit is
                if (!is_null($limit) && $limit <= $bookingCount) {
                    break;
                }
            }
        }

        // entries still empty? Then we can quit here
        if (empty($entries)) {
            return $entries;
        }

        // clip the front, but only if we filter by from date
        if (!$dontClip && $fromDate !== 0) {
            $entries[] = BookingFactory::getBooking('', Clipping::CLIPPING_TAG_FRONT, $fromDate);

            // move some bookings into the past to get the startbooking of a potential task we might need
            for ($i = $bookingKey + 1; $i < count($rawEntries); $i++) {
                if (empty(trim($rawEntries[$i]))) {
                    continue;
                }
                $entry = explode(self::SEPARATOR, trim($rawEntries[$i], ' |'));
                $comment = isset($entry[2]) ? $entry[2] : '';
                $booking = BookingFactory::getBooking($comment, $entry[1], $entry[0]);
                $entries[] = $booking;
                // break after the first non-meta booking
                if (!$booking->isMetaBooking()) {
                    break;
                }
            }
        }

        return $entries;
    }
}
