<?php

/**
 * \Wicked\Timely\FunctionalTest
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

namespace Wicked\Timely;

use Wicked\Timely\Entities\TaskFactory;

/**
 * Functional tests
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testFileStorageTaskGeneration()
    {
        // test content for the file storage
        $testContent = '2016-07-26 18:38:16 | --ps-- | break;
            2016-07-26 17:57:36 | TEST-10 | work;
            2016-07-26 17:57:35 | --pe-- | ;
            2016-07-26 17:34:52 | --ps-- | break;
            2016-07-26 17:33:46 | TEST-9 | work;
            2016-07-26 15:18:38 | TEST-8 | work;
            2016-07-26 14:40:15 | --pe-- | ;
            2016-07-26 14:29:27 | --ps-- | break;
            2016-07-26 14:24:25 | TEST-7 | work;
            2016-07-26 12:32:38 | --pe-- | ;
            2016-07-26 12:09:41 | --ps-- | break;
            2016-07-26 12:05:25 | TEST-6 | work;
            2016-07-26 10:59:29 | TEST-5 | work;
            2016-07-26 10:12:26 | TEST-4 | work;
            2016-07-26 10:00:26 | TEST-3 | work;
            2016-07-26 09:55:58 | TEST-2 | work;
            2016-07-26 09:53:48 | --pe-- | ;
            2016-07-25 17:43:43 | --ps-- | break;
            2016-07-25 12:44:48 | TEST-1 | work;';

        // get us a mocked file storage with our content
        $classToMock = '\Wicked\Timely\Storage\File';
        /** @var \Wicked\Timely\Storage\File $mockFileStorage */
        $mockFileStorage = $this->getMockBuilder($classToMock)
            ->setMethods(array('getStorageContent'))
            ->getMock();
        $mockFileStorage->expects($this->once())
            ->method('getStorageContent')
            ->will($this->returnValue($testContent));

        // generate the tasks
        $bookings = $mockFileStorage->retrieve();
        $tasks = TaskFactory::getTasksFromBookings($bookings);

        // first test: count the tasks
        $this->assertCount(10, $tasks);

        // the expected duration results
        $expectedDurations = array(
            'TEST-1' => 18065, // 4h 58m 55s + 2m 10s
            'TEST-2' => 268, // 4m 28s
            'TEST-3' => 720, // 12m
            'TEST-4' => 2823, // 47m 3s
            'TEST-5' => 3956, // 1h 5m 56s
            'TEST-6' => 6963, // 4m 16s + 1h 51m 47s
            'TEST-7' => 2605, // 5m 2s + 38m 23s
            'TEST-8' => 8108, // 2h 15m 8s
            'TEST-9' => 67, // 1m 6s + 1s
            'TEST-10' => 2440 // 40m 40s
        );

        // the expected tasks
        $expectedTicketIds = array_keys($expectedDurations);
        // iterate the tasks and check their ticketIds
        foreach ($tasks as $task) {
            $this->assertContains($task->getStartBooking()->getTicketId(), $expectedTicketIds);
        }

        // iterate the tasks and check their duration
        foreach ($tasks as $task) {
            $this->assertEquals($expectedDurations[$task->getStartBooking()->getTicketId()], $task->getDuration());
        }
    }


}