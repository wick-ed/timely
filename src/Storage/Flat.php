<?php

/**
 * \Wicked\Timely\Storage\Flat
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

namespace Wicked\Timely\Storage;

use Wicked\Timely\Entities\Booking;

/**
 * Flat storage
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Flat
{

    /**
     * Name of the log file
     *
     * @var string DATA_NAME
     */
    const DATA_NAME = 'timely-log.txt';

    /**
     * Path to the log file
     *
     * @var string $logFilePath
     */
    protected $logFilePath;

    /**
     *
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->logFilePath = realpath(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . self::DATA_NAME);
    }

    /**
     *
     * @param Booking $booking
     */
    public function store(Booking $booking)
    {
        $path = $this->getLogFilePath();
        file_put_contents($path, $booking->__toString() . file_get_contents($path));
    }

    /**
     *
     *
     *
     * @return \Wicked\Timely\Entities\Booking
     */
    public function retrieve($ticketId)
    {

    }

    /**
     *
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function retrieveAll()
    {

    }
}
