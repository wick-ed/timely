<?php

/**
 * \Wicked\Timely\Formatter\Flat
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
class Flat implements FormatterInterface
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
     * Formats a booking into a string
     *
     * @param \Wicked\Timely\Entities\Booking[]|\Wicked\Timely\Entities\Booking $bookings
     */
    public function toString($bookings)
    {
        // if we do not get an array make one
        if (!is_array($bookings)) {
            $bookings = array($bookings);
        }

        // iterate the array and create the formatted string
        $result = '';
        foreach ($bookings as $booking) {
            $result .= implode(
                self::SEPARATOR,
                array(
                    $booking->getTime(),
                    $booking->getTicketId(),
                    $booking->getComment()
                )
            ) . self::LINE_BREAK . '
';
        }
        return $result;
    }
}
