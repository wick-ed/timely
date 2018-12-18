<?php

/**
 * \Wicked\Timely\Entities\Pause
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
 * Pause entity
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Pause extends Booking
{

    /**
     * Constant for the pause start tag
     *
     * @var string PAUSE_TAG_START
     */
    const PAUSE_TAG_START = '--ps--';

    /**
     * Constant for the pause end tag
     *
     * @var string PAUSE_TAG_END
     */
    const PAUSE_TAG_END = '--pe--';

    /**
     * Default constructor
     *
     * @param string      $comment  Comment for the pause
     * @param boolean     $resuming Whether or not the pause has ended
     * @param null|string $time     Time of this booking
     */
    public function __construct($comment = '', $resuming = false, $time = null)
    {
        // add the pause tags as special meta ticket IDs
        $this->metaTicketIds[] = self::PAUSE_TAG_START;
        $this->metaTicketIds[] = self::PAUSE_TAG_END;

        if ($resuming === true) {
            parent::__construct($comment, self::PAUSE_TAG_END, $time);
        } else {
            parent::__construct($comment, self::PAUSE_TAG_START, $time);
        }
    }

    /**
     * Whether or not this booking can be the start of a task
     *
     * @param boolean $includePause Whether or not pauses are included as task building bookings
     *
     * @return boolean
     */
    public function isPauseEnd()
    {
        return $this->getTicketId() === static::PAUSE_TAG_END;
    }

    /**
     * Whether or not this booking can be the start of a task
     *
     * @param boolean $includePause Whether or not pauses are included as task building bookings
     *
     * @return boolean
     */
    public function canStartTask($includePause = false)
    {
        if ($includePause) {
            return $this->getTicketId() === self::PAUSE_TAG_START;
        }
        return false;
    }

    /**
     * Whether or not this booking can be the end of a task
     *
     * @param boolean $includePause Whether or not pauses are included as task building bookings
     *
     * @return boolean
     */
    public function canEndTask($includePause = false)
    {
        if ($includePause) {
            return $this->getTicketId() === self::PAUSE_TAG_END;
        }
        return false;
    }
}
