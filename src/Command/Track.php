<?php

/**
 * \Wicked\Timely\Command\Track
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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\Entities\Booking;
use Wicked\Timely\Storage\StorageFactory;
use Wicked\Timely\Entities\Pause as PauseEntity;

/**
 * Class for the "track" command. Command is used to track time bookings
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Track extends Command
{

    /**
     * Constants used within this command
     *
     * @var string
     */
    const COMMAND_NAME = 'track';

    /**
     * Constants for arguments
     *
     * @var string
     */
    const ARGUMENT_TICKET_IDENTIFIER = 'ticket';
    const ARGUMENT_TRACKING_COMMENT = 'comment';

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
        ->setDescription('Track an activity you just started.')
        ->addArgument(
            static::ARGUMENT_TICKET_IDENTIFIER,
            InputArgument::REQUIRED,
            'Ticket identifier to track times for, such as a ticket ID from your ticket system.'
        )
        ->addArgument(
            static::ARGUMENT_TRACKING_COMMENT,
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Comment to go along with your tracking such as "Refactoring for better testability"'
        )
        ->setHelp(<<<'EOF'
The <info>%command.name%</info> command tracks an activity you just started
This activity has to be in relation to an identifier such as a ticket ID from your ticket system.
Although this is only to structure your tracked times, it will be used when interaction with an actual ticket system.

An example call would be:

  <info>timely %command.name% FOO-42 Doing some stuff now</info>
EOF
            )
        ;
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
        // get the input arguments
        $ticket = $input->getArgument('ticket');
        $comment = implode(' ', $input->getArgument('comment'));

        $result = 'Tracking successful';
        try {
            // create a new booking instance
            $booking = new Booking($comment, $ticket);

            // get the configured storage instance and store the booking
            $storage = StorageFactory::getStorage();
            $lastBooking = $storage->retrieveLast(true);
            if ($lastBooking->getTicketId() === PauseEntity::PAUSE_TAG_START) {
                // create a new pause instance
                $now = new \DateTime();
                $pause = new PauseEntity('', true, $now->getTimestamp()-1);
                $storage->store($pause);
            }
            $storage->store($booking);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        // write output
        $output->writeln($result);
    }
}
