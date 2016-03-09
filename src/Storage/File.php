<?php

/**
 * \Wicked\Timely\Storage\File
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
use Wicked\Timely\Formatter\FormatterFactory;

/**
 * File storage
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class File
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
        // get the formatter and convert to string
        $formatter = FormatterFactory::getFormatter();
        $bookString = $formatter->toString($booking);

        // write the new booking to the beginning of the file
        $path = $this->getLogFilePath();
        file_put_contents($path, $bookString . file_get_contents($path));
    }

    /**
     *
     *
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function retrieve($ticketId)
    {
        // get the raw entries
        $rawData = file_get_contents($this->getLogFilePath());
        $rawEntries = explode(self::LINE_BREAK, $rawData);
        // itarate them and generate the entities
        $entries = array();
        foreach ($rawEntries as $rawEntry) {
            // get the potential entry and filter them by ticket ID
            $entry = explode(self::SEPARATOR, $rawEntry);
            if (isset($entry[1]) && $entry[1] === $ticketId) {
                $entries[] = new Booking($entry[2], $entry[1], $entry[0]);
            }
        }
        return $entries;
    }

    /**
     *
     *
     * @return \Wicked\Timely\Entities\Booking[]
     */
    public function retrieveAll()
    {
        $rawData = file_get_contents($path);
        $rawEntries = explode(self::LINE_BREAK, $rawData);
    }
}
