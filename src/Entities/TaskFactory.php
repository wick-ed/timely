<?php

/**
 * \Wicked\Timely\Entities\TaskFactory
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
 * Task factory
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class TaskFactory
{

    /**
     * Generates tasks from a set of bookings
     *
     * @param array   $bookings       Set of bookings to generate tasks from
     * @param boolean $squashMultiple Whether or not to squash several tasks which are based on the same ticket
     * @param boolean $includePauses  Whether or not to include pause based tasks
     *
     * @return \Wicked\Timely\Entities\Task[]
     */
    public static function getTasksFromBookings(array $bookings, $squashMultiple = false, $includePauses = false)
    {
        // iterate the bookings and collect by task
        $tasks = array();
        $startBooking = null;
        $intermediateBookings = array();
        $bookingsCount = count($bookings) - 1;
        for ($i = $bookingsCount; $i >= 0; $i --) {
            $booking = $bookings[$i];
            // set the start booking
            if (is_null($startBooking) && $booking->canStartTask($includePauses)) {
                $startBooking = $booking;
            } elseif (is_null($startBooking)) {
                continue;
            } else {
                // check if the task is finished here
                if ($booking->canEndTask($includePauses)) {
                    // create a new task entity and collect it
                    $tasks[] = new Task($startBooking, $booking, array_reverse($intermediateBookings));
                    // reset the tmp vars
                    $startBooking = $booking->canStartTask($includePauses) ? $booking : null;
                    $intermediateBookings = array();

                } else {
                    // collect as intermediate
                    $intermediateBookings[] = $booking;
                }
            }
        }
        // if we are out of booking but have a pending task we will close it
        if (!is_null($startBooking) && $booking->getTicketId() !== $startBooking->getTicketId()) {
            $tasks[] = new Task($startBooking, $booking, array_reverse($intermediateBookings));
        }
        // return what we got
        return $tasks;
    }
}
