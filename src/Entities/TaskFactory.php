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
     *
     * @param array $bookings
     * @param boolean $squashMultiple
     */
    public function getTasksFromBookings(array $bookings, $squashMultiple = false)
    {
        // iterate the bookings and collect by task
        $tasks = array();
        $startBooking = null;
        $intermediateBookings = array();
        foreach ($bookings as $booking) {
            // check if the task is finished here
            if ($booking->getTicketId() !== $startBooking->getTicketId() && $booking->getTicketId() !== Pause::PAUSE_TAG_START && $booking->getTicketId() !== Pause::PAUSE_TAG_END) {
                // create a new task entity and collect it
                $tasks[] = new Task($startBooking, $booking, $intermediateBookings);
                // reset the tmp vars
                $startBooking = $booking;
                $intermediateBookings = array();

            } else {
                // collect as intermediate
                $intermediateBookings[] = $booking;
            }
        }
    }
}
