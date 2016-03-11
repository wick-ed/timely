<?php

/**
 * \Wicked\Timely\Formatter\Grouped
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

namespace Wicked\Timely\Formatter;

use Wicked\Timely\Entities\Booking;

/**
 * Flat storage
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Grouped
{

    /**
     * Default character sequence for segment separation
     *
     * @var string SEPARATOR
     */
    const SEPARATOR = ' | ';

    /**
     *
     * @param Booking $booking
     */
    public function toString(array $bookings)
    {
        // if we do not get an array make one
        if (!is_array($bookings)) {
            $bookings = array($bookings);
        }

        // iterate the bookings and sort them by ticket
        $groups = array();
        foreach ($bookings as $booking) {
            // prepare for collection
            if (!isset($groups[$booking->getTicketId()])) {
                $groups[$booking->getTicketId()] = array();
            }
            // collect the bookings
            $groups[$booking->getTicketId()][] = $booking;
        }

        // generate the group string
        $result = '';
        foreach ($groups as $ticketId => $tickets) {
            $result .= $this->renderGroup($ticketId, $tickets);
        }
        // return the string
        return $result;
    }

    /**
     *
     * @param string $bookingString
     */
    public function toBooking($bookingString)
    {
    }

    /**
     *
     * @param unknown $ticketId
     * @param array $bookings
     *
     * @return string
     */
    protected function renderGroup($ticketId, array $bookings)
    {
        // collect some vital numbers
        $total = 0;
        $ticketList = '';
        foreach ($bookings as $booking) {
            // create the entry string
            $ticketList .= implode(
                self::SEPARATOR,
                array(
                    $booking->getTime(),
                    $booking->getTicketId(),
                    $booking->getComment()
                )
                ) . '
    ';
        }

        // we also need the first and last element of the array
        $lastBooking = reset($bookings);
        $firstBooking = end($bookings);

        // begin the string generation
        $result = $ticketId . '     ' .  $firstBooking->getTime() . ' -> ' . $lastBooking->getTime() . '
====================================================
    ' . $ticketList;

        // print the total and end with another linebreak without indention to break groups apart
        $result .= '-------------------------------------------------
    ' . $total . '

';
        // return the string
        return $result;
    }
}
