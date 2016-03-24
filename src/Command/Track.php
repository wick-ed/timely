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
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\Entities\Booking;
use Wicked\Timely\Storage\StorageFactory;

/**
 * Class for the "track" command. Command is used to track time bookings
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Track extends Command
{

    /**
     * Configures the "show" command
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('track')
        ->setDescription('Track times')
        ->addArgument(
            'ticket',
            InputArgument::REQUIRED,
            'Ticket to track times for'
            )
        ->addArgument(
            'comment',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Comment for tracking entry'
            );
    }

    /**
     * Execute the command
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
            $storage->store($booking);

        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        // write output
        $output->writeln($result);
    }
}
