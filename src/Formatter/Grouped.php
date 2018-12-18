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

use Wicked\Timely\Entities\Task;
use Wicked\Timely\Entities\TaskFactory;
use Wicked\Timely\Helper\Date;

/**
 * Flat storage
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Grouped implements FormatterInterface
{

    /**
     * Default character sequence for segment separation
     *
     * @var string SEPARATOR
     */
    const SEPARATOR = ' | ';

    /**
     * The total duration of all tasks grouped together
     *
     * @var integer $totalDuration
     */
    protected $totalDuration;

    /**
     * Formats a booking into a string
     *
     * @param \Wicked\Timely\Entities\Booking[]|\Wicked\Timely\Entities\Booking $bookings The bookings to format
     *
     * @return string
     */
    public function toString($bookings)
    {
        // if we do not get an array make one
        if (!is_array($bookings)) {
            $bookings = array($bookings);
        }

        // create the tasks from the bookings
        $tasks = TaskFactory::getTasksFromBookings($bookings);
        // iterate the tasks and sort them by ticket
        $groups = array();
        /** @var Task $task */
        foreach ($tasks as $task) {
            // skip tasks which are used to describe meta bookings
            if ($task->getStartBooking()->isMetaBooking()) {
                continue;
            }
            // prepare for collection
            $ticketId = $task->getTicketId();
            // prepare the group if needed
            if (!isset($groups[$ticketId])) {
                $groups[$ticketId] = array();
            }
            // collect the grouped tasks
            $groups[$ticketId][] = $task;
        }

        // generate the group string
        $result = '';
        foreach ($groups as $ticketId => $groupedTasks) {
            $result .= $this->renderGroup($ticketId, $groupedTasks);
        }

        // print the total amount of time spent
        $result .= '
====================================================
Total: ca. ' . Date::secondsToUnits(Date::roundByInterval($this->totalDuration, 900)) . '
====================================================';

        // return the string
        return $result;
    }

    /**
     * Render a certain group of tasks
     *
     * @param string                         $ticketId Ticket id to render a group for
     * @param \Wicked\Timely\Command\Track[] $tasks    Set of tasks to render a group for
     *
     * @return string
     */
    protected function renderGroup($ticketId, array $tasks)
    {
        // collect some vital numbers
        $total = 0;
        $ticketList = '';
        foreach ($tasks as $task) {
            // create the entry string
            $booking = $task->getStartBooking();
            $ticketList .= implode(
                self::SEPARATOR,
                array(
                    $task->getStartTime(),
                    $task->getTicketId(),
                    Date::secondsToUnits($task->getDuration()),
                    $task->getComment()
                )
            ) . '
    ';
            $total += $task->getDuration();
        }

        // we also need the first and last element of the array
        $startTime = reset($tasks)->getStartTime();
        $lastBooking = end($tasks)->getEndBooking();

        // begin the string generation
        $result = $ticketId . '     ' .  $startTime . ' -> ' . $lastBooking->getTime() . '
====================================================
    ' . $ticketList;

        // print the total and end with another linebreak without indention to break groups apart
        $result .= '-------------------------------------------------
    ' . Date::secondsToUnits(Date::roundByInterval($total, 900)) . '

';
        $this->totalDuration += $total;

        // return the string
        return $result;
    }
}
