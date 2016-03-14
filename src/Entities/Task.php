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
     * Id of a potential ticket the booking is for
     *
     * @var string|null $ticketId
     */
    protected $startBooking;

    /**
     * Comment regarding the current booking
     *
     * @var string $comment
     */
    protected $endBooking;

    /**
     *
     * @var unknown
     */
    protected $intermediateBookings;

    /**
     *
     * @var unknown
     */
    protected $duration;

    /**
     *
     */
    public function getStartBooking()
    {
        return $this->startBooking;
    }

    /**
     *
     */
    public function getEndBooking()
    {
        return $this->endBooking;
    }

    /**
     *
     */
    public function getIntermediateBookings()
    {
        return $this->intermediateBookings;
    }

    /**
     *
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     *
     * @param unknown $startBooking
     * @param unknown $endBooking
     * @param unknown $intermediateBookings
     */
    public function __construct($startBooking, $endBooking, $intermediateBookings)
    {
        // set the properties
        $this->startBooking = $startBooking;
        $this->endBooking = $endBooking;
        $this->intermediateBookings = $intermediateBookings;
        // calculate the duration
        $this->duration = $this->calculateDruation($startBooking, $endBooking, $intermediateBookings);
    }

    /**
     *
     */
    protected function calculateDruation($startBooking, $endBooking, $intermediateBookings)
    {
        // get the raw time without breaks and such
        $rawTime = strtotime($endBooking->getTime()) - strtotime($startBooking->getTime());
        // substract the breaks
        return $rawTime;
    }
}
