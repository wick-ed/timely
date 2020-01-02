<?php

/**
 * \Wicked\Timely\Command\Push
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Command;

use JiraRestApi\JiraException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\Entities\Booking;
use Wicked\Timely\Entities\TaskFactory;
use Wicked\Timely\Formatter\FormatterFactory;
use Wicked\Timely\PushServices\PushServiceFactory;
use Wicked\Timely\Storage\StorageFactory;

/**
 * Class for the "track" command. Command is used to track time bookings
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Push extends AbstractReadCommand
{

    /**
     * Constants used within this command
     *
     * @var string
     */
    const COMMAND_NAME = 'push';

    /**
     * Configures the "track" command
     *
     * @return void
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName(static::COMMAND_NAME)
        ->setDescription('Pushes booked times against the configured remote')
        ->setHelp(<<<'EOF'
The <info>%command.name%</info> command is used to push tracked times to an external time keeping service.
Jira being an example of a supported service.

The command has the same syntax and usability as the <info>show</info> command.
On execution the command will use the service's internal format to process all tracked times that a similar <info>show</info> command would have displayed.

The following command would create e.g. Jira worklogs for yesterday's tasks:

<info>timely %command.name% yesterday</info>

The <info>%command.name%</info> command keeps track of already pushed time trackings so nothing gets pushed twice.
EOF
        )
        ;
        // add all the read options from the abstract super class
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  The command input
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output
     *
     * @return void
     *
     * {@inheritDoc}
     * @throws JiraException
     * @throws \JsonMapper_Exception
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get the ticket
        $ticket = $input->getArgument('ticket');

        // we might need framing dates
        $toDate = null;
        $fromDate = null;

        // there might be a limit
        $limit = null;

        // prepare all the input options
        $this->prepareInputParams($input, $ticket, $fromDate, $toDate, $limit);

        // filter by ticket if given
        /** @var \Wicked\Timely\Storage\StorageInterface $storage */
        $storage = StorageFactory::getStorage();
        $bookings = $storage->retrieve($ticket, $toDate, $fromDate, $limit);

        // return if we did not find any bookings
        if (empty($bookings)) {
            $output->write('No bookings found, nothing to push ...', true);
            return;
        }

        // get tasks from bookings
        $tasks = TaskFactory::getTasksFromBookings($bookings);

        // get our issue service and push the tasks
        $bookingsPushed = array();
        $pushService = PushServiceFactory::getService($output);
        foreach ($tasks as $task) {
            // Already pushed to jira? take next one
            if ($task->isPushed()) {
                continue;
            }

            try {
                $result = $pushService->push($task);
                if ($result) {
                    $bookingsPushed[] = $task->getStartBooking();
                    $output->writeln(
                        sprintf(
                            'PUSHED %s : %s > %s',
                            $task->getTicketId(),
                            $task->getDuration(),
                            $task->getComment()
                        )
                    );
                }
            } catch (\Exception $e) {
                $output->write(
                    sprintf(
                        '<erro>Error while pushing. Status %s, with message: "%s"</erro>',
                        $e->getCode(),
                        $e->getMessage()
                    )
                );
            }
        }

        // mark the bookings as pushed in our storage
        foreach ($bookingsPushed as $booking) {
            $storage->storePush($booking);
            $formatter = FormatterFactory::getFormatter();
            $bookString = $formatter->toString($booking);
            $output->write($bookString, true);
        }

        // if the last task has been cut off by pushing we will re-add it's start booking
        $lastTask = end($tasks);
        if ($lastTask->isOngoing() && !$lastTask->isPaused()) {
            $continuedBooking = new Booking(
                $lastTask->getComment(),
                $lastTask->getTicketId()
            );
            $storage->store($continuedBooking);
        }

        // write output
        $output->write(sprintf('Successfully pushed %s tasks.', count($bookingsPushed)), true);
    }
}
