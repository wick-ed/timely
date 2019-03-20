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
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Command;

use Wicked\Timely\DotEnvConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\Entities\TaskFactory;
use Wicked\Timely\Helper\Date;
use Wicked\Timely\Storage\StorageFactory;

/**
 * Class for the "track" command. Command is used to track time bookings
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Push extends AbstractReadCommand
{

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
        ->setDescription('Pushes booked times against the configured remote');

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
        /** @var \Wicked\Timely\Storage\StorageFactory $storage */
        $storage = StorageFactory::getStorage();
        $bookings = $storage->retrieve($ticket, $toDate, $fromDate, $limit);

        // return if we did not find any bookings
        if (empty($bookings)) {
            $output->write('No bookings found, nothing to push ...', true);
            return;
        }

        // get tasks from bookings
        $tasks = TaskFactory::getTasksFromBookings($bookings);

        // create unique identity of task to reduce duplicate tasks being pushed
        // @TODO

        // retrieve configuration
        $configuration = new DotEnvConfiguration();

        try {
            // get our issue service and push the tasks
            $issueService = new IssueService($configuration);
            foreach ($tasks as $task) {
                // create a worklog from the task
                $workLog = new Worklog();
                $workLog->setComment($task->getComment())
                    ->setStarted($task->getStartTime())
                    ->setTimeSpent(Date::secondsToUnits(Date::roundByInterval($task->getDuration(), 900)));

                // check the sanity of our worklog and discard it if there is something missing
                if (!$task->getTicketId() || empty($workLog->timeSpent) || empty($workLog->comment))
                {
                    $output->writeln('Not pushing one worklog as it misses vital information');
                    continue;
                }

                // log the worklog about to being pushed if output is verbose
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln(
                        sprintf(
                            '%s : %s > %s',
                            $task->getTicketId(),
                            $workLog->timeSpent,
                            $workLog->comment
                        )
                    );
                }

                // push to remote
                $issueService->addWorklog($task->getTicketId(), $workLog);
            }
        } catch (JiraException $e) {
            $output->write(
                sprintf(
                    'Error while pushing. Status %s, with message: "%s"',
                    $e->getCode(),
                    $e->getMessage()
                )
            );
        }


        // write output
        $output->write(sprintf('Successfully pushed %s tasks.', count($tasks)), true);
    }
}