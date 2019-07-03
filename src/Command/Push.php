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

use JiraRestApi\Configuration\ConfigurationInterface;
use Wicked\Timely\DotEnvConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\Entities\TaskFactory;
use Wicked\Timely\Formatter\FormatterFactory;
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

    /**
     * Constants used within this command
     *
     * @var string
     */
    const COMMAND_NAME = 'push';
    const KEYCHAIN_NAME = 'osxkeychain';
    const KEYCHAIN_SAVE = 'timely jira access';

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
     * @throws JiraException
     * @throws \JsonMapper_Exception
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $passwd = $this->promptSilent($output);
//        $output->writeln('fertig für heute:'.$passwd.'#');

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

        // retrieve configuration
        $configuration = new DotEnvConfiguration();
        $password = $configuration->getJiraPassword();
        if (empty($password) || strtolower($password) === self::KEYCHAIN_NAME) {
            $password = $this->getPasswordFromKeychain($output, $configuration);
            if (empty($password)) {
                return;
            }
            $configuration->setJiraPassword($password);

        }
        unset($password);
        $bookingsPushed = array();
        // get our issue service and push the tasks
        $issueService = new IssueService($configuration);
        foreach ($tasks as $task) {
            // Already pushed to jira? take next one
            if ($task->isPushed()) {
                continue;
            }
            // create a worklog from the task
            $workLog = new Worklog();
            $workLog->setComment($task->getComment())
                ->setStarted($task->getStartTime())
                ->setTimeSpent(Date::secondsToUnits(Date::roundByInterval($task->getDuration(), 900)));

            // check the sanity of our worklog and discard it if there is something missing
            if (!$task->getTicketId() || empty($workLog->timeSpent) || empty($workLog->comment)) {
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

            try {
                // push to remote
                $issueService->addWorklog($task->getTicketId(), $workLog);

                $output->writeln(
                    sprintf(
                        'PUSHED %s : %s > %s',
                        $task->getTicketId(),
                        $workLog->timeSpent,
                        $workLog->comment
                    )
                );

                $bookingsPushed[] = $task->getStartBooking();

            } catch (JiraException $e) {
                $output->write(
                    sprintf(
                        'Error while pushing. Status %s, with message: "%s"',
                        $e->getCode(),
                        $e->getMessage()
                    )
                );
            }
        }

        foreach ($bookingsPushed as $booking) {
            $storage->storePush($booking);
            $formatter = FormatterFactory::getFormatter();
            $bookString = $formatter->toString($booking);
            $output->write($bookString, true);
        }

        // write output
        $output->write(sprintf('Successfully pushed %s tasks.', count($bookingsPushed)), true);

    }

    /**
     * Prompt silent
     *
     * Interactively prompts for input without echoing to the terminal.
     * Requires a bash shell or Windows and won't work with
     * safe_mode settings (Uses `shell_exec`)
     *
     * Source: http://www.sitepoint.com/interactive-cli-password-prompt-in-php/
     *
     * @param OutputInterface $output Console output interface
     * @param string          $prompt The message to the user
     *
     * @return string
     */
    protected function promptSilent(OutputInterface $output, $prompt = "Enter Password:")
    {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return '';
        }
        $command = "/usr/bin/env bash -c 'read -s -p \""
                   . addslashes($prompt)
                   . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        $output->writeln('');
        return $password;
    }

    /**
     * Will retrieve a stored password from the OSX keychain
     *
     * @param OutputInterface        $output        Console output interface
     * @param ConfigurationInterface $configuration Jira configuration
     *
     * @return string
     */
    private function getPasswordFromKeychain(OutputInterface $output, ConfigurationInterface $configuration)
    {
        $password = rtrim(shell_exec("security find-generic-password -s '".self::KEYCHAIN_SAVE."' -w"));
        if (empty($password)) {
            $password = $this->promptSilent($output, 'Please enter password for your jira account "'.$configuration->getJiraUser().'":');
            if (empty($password)) {
                $output->writeln('Empty password is not possible. Stop push ...');
                return '';
            }
            shell_exec('security add-generic-password -a "'.$configuration->getJiraUser().'" -s "'.self::KEYCHAIN_SAVE.'" -w "'.$password.'"');
        }
        return $password;
    }
}
