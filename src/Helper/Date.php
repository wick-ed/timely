<?php

/**
 * \Wicked\Timely\Helper\Date
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    wick-ed
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Helper;

/**
 * Date helper
 *
 * @author    wick-ed
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Date
{

    /**
     * Constant for the number of seconds in a day
     *
     * @var integer DAY_IN_SECONDS
     */
    const DAY_IN_SECONDS = 86400;

    /**
     * Constant for the number of seconds in an hour
     *
     * @var integer HOUR_IN_SECONDS
     */
    const HOUR_IN_SECONDS = 3600;

    /**
     * Constant for the number of seconds in a minute
     *
     * @var integer MINUTE_IN_SECONDS
     */
    const MINUTE_IN_SECONDS = 60;

    /**
     * Formats a timespan into a string of the format 0d 0h 0m
     *
     * @param integer $timespan The timespan in seconds to format
     *
     * @return string
     */
    public static function secondsToUnits($timespan)
    {
        // get the days
        $days = floor($timespan / self::DAY_IN_SECONDS);
        $timespan -= $days * self::DAY_IN_SECONDS;
        // get the hours
        $hours = floor($timespan / self::HOUR_IN_SECONDS);
        $timespan -= $hours * self::HOUR_IN_SECONDS;
        // get the minutes
        $minutes = round($timespan / self::MINUTE_IN_SECONDS);
        // return a formatted string
        $result = $days > 0 ? sprintf('%sd ', $days) : '';
        $result .= $hours > 0 ? sprintf('%sh ', $hours) : '';
        $result .= $minutes > 0 ? sprintf('%sm', $minutes) : '';
        return $result;
    }

    /**
     * Formats a timespan into a string of the format 0d 0h 0m
     *
     * @param integer $timespan       The timespan in seconds to format
     * @param integer $interval       The interval in seconds to round against
     * @param boolean $enforceMinimum Whether or not a configurable minimal timespan should be used
     *
     * @return string
     */
    public static function roundByInterval($timespan, $interval, $enforceMinimum = true)
    {
        // it could happen that we booked for less than our interval, so it would be rounded down to zero.
        // By default we should prevent that
        if ($enforceMinimum && $timespan < $interval) {
            return $interval;
        }

        // only round if we aren't spot on
        $overhead = $timespan % $interval;
        if ($overhead === 0) {
            return $timespan;
        }

        // round up or down based on our interval
        if ($overhead >= ($interval / 2)) {
            $timespan += ($interval - $overhead);
        } else {
            $timespan -= $overhead;
        }

        return $timespan;
    }
}
