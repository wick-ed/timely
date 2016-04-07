<?php

/**
 * \Wicked\Timely\Entities\Task
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
 * Task entity
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Task
{

    /**
     * First booking of this task
     *
     * @var \Wicked\Timely\Entities\Booking $startBooking
     */
    protected $startBooking;

    /**
     * Last booking of this task
     *
     * @var \Wicked\Timely\Entities\Booking $endBooking
     */
    protected $endBooking;

    /**
     * Bookings within this task
     *
     * @var \Wicked\Timely\Entities\Booking[] $intermediateBookings
     */
    protected $intermediateBookings;

    /**
     * Intermediate tasks, done within this task instance
     *
     * @var \Wicked\Timely\Entities\Task[] $intermediateTasks
     */
    protected $intermediateTasks;

    /**
     * The task's duration
     *
     * @var integer $duration
     */
    protected $duration;

    /**
     * Getter for the first booking of the task instance
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public function getStartBooking()
    {
        return $this->startBooking;
    }

    /**
     * Getter for the last booking of the task instance
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public function getEndBooking()
    {
        return $this->endBooking;
    }

    /**
     * Getter for the intermediate bookings
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function getIntermediateBookings()
    {
        return $this->intermediateBookings;
    }

    /**
     * Getter for the task duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Default constructor
     *
     * @param \Wicked\Timely\Entities\Booking   $startBooking         The first booking of the task
     * @param \Wicked\Timely\Entities\Booking   $endBooking           The last booking of the task
     * @param \Wicked\Timely\Entities\Booking[] $intermediateBookings Bookings within this task
     */
    public function __construct($startBooking, $endBooking, $intermediateBookings)
    {
        // set the properties
        $this->startBooking = $startBooking;
        $this->endBooking = $endBooking;
        $this->intermediateBookings = $intermediateBookings;
        // we will need the tasks which are built up by the intermediate bookings
        $this->intermediateTasks = TaskFactory::getTasksFromBookings(array_merge($intermediateBookings, array($endBooking)), false, true);
        // calculate the duration
        $this->duration = $this->calculateDuration($startBooking, $endBooking, $this->intermediateTasks);
    }

    /**
     * Calculates the duration of a task by given bookings
     *
     * @param \Wicked\Timely\Entities\Booking   $startBooking      The first booking of the task
     * @param \Wicked\Timely\Entities\Booking   $endBooking        The last booking of the task
     * @param \Wicked\Timely\Entities\Booking[] $intermediateTasks Bookings within this task
     *
     * @return integer
     */
    protected function calculateDuration($startBooking, $endBooking, $intermediateTasks)
    {
        // get the raw time without breaks and such
        $rawTime = strtotime($endBooking->getTime()) - strtotime($startBooking->getTime());
        // substract the breaks
        foreach ($intermediateTasks as $intermediateTask) {
            $rawTime -= $intermediateTask->getDuration();
        }
        // return what we got
        return $rawTime;
    }
}
