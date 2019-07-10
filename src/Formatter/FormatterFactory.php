<?php

/**
 * \Wicked\Timely\Formatter\FormatterFactory
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

/**
 * Formatter factory
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class FormatterFactory
{

    /**
     * Constant for the output channel
     *
     * @var string OUTPUT_CHANNEL
     */
    const OUTPUT_CHANNEL = 'formatter.channel.output';

    /**
     * Constant for the storage channel
     *
     * @var string STORAGE_CHANNEL
     */
    const STORAGE_CHANNEL = 'formatter.channel.storage';

    /**
     * Getter for the right formatter
     *
     * @param null|string $channel Channel to get a formatter for
     *
     * @return \Wicked\Timely\Formatter\FormatterInterface
     */
    public static function getFormatter($channel = null)
    {
        switch ($channel) {
            case self::OUTPUT_CHANNEL:
                return new Grouped();
                break;

            case self::STORAGE_CHANNEL:
                return new Flat();
                break;

            default:
                return new Flat();
                break;
        }
    }
}
