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

    public function fileStorageTaskGenerationProvider()
    {
        return array(
            array('2016-07-26 18:38:16 | --ps-- | break;
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
            2016-07-25 12:44:48 | TEST-1 | work;',
                10,
                array(
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
                )),
            array('2016-07-26 13:41:49 | --ps-- | break;
            2016-07-26 10:38:17 | TEST-3 | work;
            2016-07-26 10:18:45 | TEST-2 | work;
            2016-07-26 08:37:56 | --pe-- | ;
            2016-07-25 18:48:48 | --ps-- | break;
            2016-07-25 16:13:53 | --pe-- | ;
            2016-07-25 16:00:58 | --ps-- | break;
            2016-07-25 14:20:57 | --pe-- | ;
            2016-07-25 13:42:35 | --ps-- | break;
            2016-07-25 10:18:04 | --pe-- | ;
            2016-07-23 18:10:08 | --ps-- | break;
            2016-07-23 13:00:02 | --pe-- | ;
            2016-07-23 12:30:24 | --ps-- | break;
            2016-07-23 10:29:55 | TEST-1 | work;',
                3,
                array(
                    'TEST-1' => 59451, // 2h 29s + 5h 10m 6s + 3h 24m 31s + 1h 40m 1s + 2h 34m 55s + 1h 40m 49s
                    'TEST-2' => 1172, // 19m 32s
                    'TEST-3' => 11012 // 3h 3m 32s
                )
            )
        );
    }

    /**
     * @dataProvider fileStorageTaskGenerationProvider
     */
    public function testFileStorageTaskGeneration($testContent, $taskCount, $expectedDurations)
    {
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
        $this->assertCount($taskCount, $tasks);

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