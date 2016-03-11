<?php

/**
 * \Wicked\Timely\Entities\Booking
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
 * Booking entity
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Booking
{

    const DEFAULT_DATE_FORMAT = 'Y-m-d H:i';

    /**
     * Id of a potential ticket the booking is for
     *
     * @var string|null $ticketId
     */
    protected $ticketId;

    /**
     * Comment regarding the current booking
     *
     * @var string $comment
     */
    protected $comment;

    /**
     *
     * @var unknown
     */
    protected $time;

    /**
     *
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     *
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     *
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     *
     * @param unknown $comment
     * @param unknown $ticketId
     */
    public function __construct($comment, $ticketId = '', $time = null)
    {
        // get the arguments
        $this->ticketId = trim($ticketId);
        $this->comment = trim($comment);

        // get the current date and time (if not given)
        if (is_null($time)) {
            $this->time = date(self::DEFAULT_DATE_FORMAT);
        } else {
            $this->time = trim($time);
        }
    }
}
