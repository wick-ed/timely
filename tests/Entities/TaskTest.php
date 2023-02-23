<?php

/**
 * \Wicked\Timely\Entities\TaskTest
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
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Entities;

use PHPUnit\Framework\TestCase;

/**
 * Unit-Test class for the "Task" entity
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class TaskTest extends TestCase
{

    /**
     * Basic test for the constructor
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(
            '\Wicked\Timely\Entities\Task',
            new Task(
                new Booking('test for a start booking'),
                new Booking('test for an end booking'),
                array()
            )
        );
    }

    /**
     * Data provider for the testGetDurationWithNoSpecialCase method
     *
     * @return array[]
     */
    public static function getDurationWithNoSpecialCaseProvider(): array
    {
        return array(
            array(1465128900, 1465128998, 98),
            array(1465125998, 1465128998, 3000),
        );
    }

    /**
     * Testing if the duration is calculated and returned correctly
     *
     * @param int $startTime Start time in seconds
     * @param int $endTime   End time in seconds
     * @param int $duration  Duration in seconds
     *
     * @return void
     *
     * @dataProvider getDurationWithNoSpecialCaseProvider
     */
    public function testGetDurationWithNoSpecialCase(int $startTime, int $endTime, int $duration): void
    {
        // get two bookings to make up the task
        $startBooking = new Booking(
            'test for a start booking',
            'TEST-1',
            date(Booking::DEFAULT_DATE_FORMAT, $startTime)
        );
        $endBooking = new Booking(
            'test for a end booking',
            'TEST-2',
            date(Booking::DEFAULT_DATE_FORMAT, $endTime)
        );

        // make the assertion
        $testTask = new Task($startBooking, $endBooking, array());
        $this->assertEquals($duration, $testTask->getDuration());
    }

    /**
     * Testing if the duration is calculated and returned correctly
     *
     * @return void
     */
    public function testGetDurationWithOneIntermediateTask(): void
    {
        // get two bookings to make up the task
        $startBooking = new Booking(
            'test for a start booking',
            'TEST-1',
            date(Booking::DEFAULT_DATE_FORMAT, 1465125998)
        );
        $endBooking = new Booking(
            'test for an end booking',
            'TEST-2',
            date(Booking::DEFAULT_DATE_FORMAT, 1465128998)
        );

        // create two intermediate bookings to simulate a pause
        $pauseStart = new Pause('pause start', false, 1465126998);
        $pauseEnd = new Pause('pause end', true, 1465127998);

        // make the assertion
        $testTask = new Task($startBooking, $endBooking, array($pauseEnd, $pauseStart));
        $this->assertEquals(2000, $testTask->getDuration());
    }

    /**
     * Testing if the duration is calculated and returned correctly
     *
     * @return void
     */
    public function testGetDurationWithTwoIntermediateTasks(): void
    {
        // get two bookings to make up the task
        $startBooking = new Booking(
            'test for a start booking',
            'TEST-1',
            date(Booking::DEFAULT_DATE_FORMAT, 1465122998)
        );
        $endBooking = new Booking(
            'test for an end booking',
            'TEST-2',
            date(Booking::DEFAULT_DATE_FORMAT, 1465128998)
        );

        // create two intermediate bookings to simulate a pause
        $pauseStart1 = new Pause('pause start', false, 1465123998);
        $pauseEnd1 = new Pause('pause end', true, 1465124498);
        $pauseStart2 = new Pause('pause start', false, 1465125998);
        $pauseEnd2 = new Pause('pause end', true, 1465126998);

        // make the assertion
        $testTask = new Task($startBooking, $endBooking, array($pauseEnd1, $pauseStart1, $pauseEnd2, $pauseStart2));
        $this->assertEquals(4500, $testTask->getDuration());
    }
}
