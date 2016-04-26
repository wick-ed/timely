<?php

/**
 * \Wicked\Timely\Entities\Clipping
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
 * Clipping entity
 * A special type of booking which acts as a boundary for tasks which stretch over the time period shown to the user
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Clipping extends Booking
{

    /**
     * Constant for the clipping mark tag
     *
     * @var string CLIPPING_TAG_FRONT
     */
    const CLIPPING_TAG_FRONT = '--cf--';

    /**
     * Constant for the clipping mark tag
     *
     * @var string CLIPPING_TAG_REAR
     */
    const CLIPPING_TAG_REAR = '--cr--';

    /**
     * Default constructor
     *
     * @param boolean     $clipFront Whether or not this clipping comes up front or at the end of booking list
     * @param null|string $time      Time of this booking
     */
    public function __construct($clipFront, $time = null)
    {
        // add the clipping tags as special meta ticket IDs
        $this->metaTicketIds[] = self::CLIPPING_TAG_FRONT;
        $this->metaTicketIds[] = self::CLIPPING_TAG_REAR;

        if ($clipFront) {
            parent::__construct('', self::CLIPPING_TAG_FRONT, $time);
        } else {
            parent::__construct('', self::CLIPPING_TAG_REAR, $time);
        }
    }
}
