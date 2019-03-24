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
     * @param bool        $pushed   Pushed to jira worklog
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public static function getBooking($comment, $ticketId = '', $time = null, $pushed=false)
    {
        switch ($ticketId) {

            case Clipping::CLIPPING_TAG_FRONT:
                return new Clipping(true, $time);
                break;

            case Clipping::CLIPPING_TAG_REAR:
                return new Clipping(false, $time);
                break;

            case Pause::PAUSE_TAG_START:
                return new Pause($comment, false, $time);
                break;

            case Pause::PAUSE_TAG_END:
                return new Pause($comment, true, $time);
                break;

            default:
                return new Booking($comment, $ticketId, $time, $pushed);
                break;
        }
    }

    /**
     * Returns all known meta booking ticket IDs
     *
     * @return array
     */
    public static function getAllMetaTicketIds()
    {
        return array(
            Clipping::CLIPPING_TAG_FRONT => Clipping::CLIPPING_TAG_FRONT,
            Clipping::CLIPPING_TAG_REAR => Clipping::CLIPPING_TAG_REAR,
            Pause::PAUSE_TAG_START => Pause::PAUSE_TAG_START,
            Pause::PAUSE_TAG_END => Pause::PAUSE_TAG_END
        );
    }
}
