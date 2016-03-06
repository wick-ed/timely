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

    /**
     *
     * @var unknown
     */
    const LINE_BREAK = ';';

    /**
     *
     * @var unknown
     */
    const SEPARATOR = ' | ';

    /**
     *
     * @var unknown
     */
    protected $ticketId;

    /**
     *
     * @var unknown
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

    public function __construct($ticketId, $comment)
    {
        // get the arguments
        $this->ticketId = $ticketId;
        $this->comment = $comment;

        // get the current date and time
        $this->time = time();
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return implode(
            self::SEPARATOR,
            array(
                $this->getTime(),
                $this->getTicketId(),
                $this->getComment()
            )
        ) . self::LINE_BREAK . '
';
    }
}
