<?php

/**
 * \Wicked\Timely\Entities\Booking
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

namespace Wicked\Timely\Entities;

/**
 * Booking entity
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Booking
{

    /**
     * Default date format
     *
     * @var string DEFAULT_DATE_FORMAT
     */
    const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Id of a potential ticket the booking is for
     *
     * @var string|null $ticketId
     */
    protected $ticketId;

    /**
     * Comment regarding the current booking
     *
     * @var string $comment
     */
    protected $comment;

    /**
     * Time this booking was made
     *
     * @var string $time
     */
    protected $time;

    /**
     * A list of special ticket IDs which identify a meta ticket
     *
     * @var string[] $metaTicketIds
     */
    protected $metaTicketIds = array();

    /**
     * Getter for the default date format
     *
     * @return string
     */
    public function getDefaultDateFormat()
    {
        return self::DEFAULT_DATE_FORMAT;
    }

    /**
     * Getter for the booking time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Getter for the booked ticket id
     *
     * @return string
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     * Getter for the booking comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Whether or not this booking is a meta booking used
     * to either create workflows or groupings of bookings and time durations
     *
     * @return boolean
     */
    public function isMetaBooking()
    {
        return in_array($this->getTicketId(), $this->getMetaTicketIds());
    }

    /**
     * Getter for the meta ticket IDs
     *
     * @return string[]
     */
    public function getMetaTicketIds()
    {
        return $this->metaTicketIds;
    }

    /**
     * Default constructor
     *
     * @param string              $comment  Comment for the booking
     * @param string              $ticketId [optional] Optional ticket ID. Defaults to an empty string
     * @param null|string|integer $time     [optional] Time of this booking. Defaults to NULL
     */
    public function __construct($comment, $ticketId = '', $time = null)
    {
        // get the arguments
        $this->ticketId = trim($ticketId);
        $this->comment = trim($comment);

        // get the current date and time (if not given)
        if (is_null($time) || is_integer($time)) {
            $this->time = date(self::DEFAULT_DATE_FORMAT, $time);
        } else {
            $this->time = trim($time);
        }
    }
}
