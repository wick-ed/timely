<?php

/**
 * \Wicked\Timely\Entities\BookingFactory
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
 * Booking factory
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class BookingFactory
{

    /**
     * Default constructor
     *
     * @param string      $comment  Comment for the booking
     * @param string      $ticketId Optional ticket ID
     * @param null|string $time     Time of this booking
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public static function getBooking($comment, $ticketId = '', $time = null)
    {
        switch ($ticketId) {

            case Pause::PAUSE_TAG_START:
                return new Pause($comment, false, $time);
                break;

            case Pause::PAUSE_TAG_END:
                return new Pause($comment, true, $time);
                break;

            default:
                return new Booking($comment, $ticketId, $time);
                break;
        }
    }
}
