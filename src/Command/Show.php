<?php

/**
 * \Wicked\Timely\Command\Show
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
use Wicked\Timely\Storage\StorageFactory;

/**
 * Class for the "show" command. Command used to print all tracked times
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Show extends Command
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
        ->setName('show')
        ->setDescription('Show tracked times')
        ->addArgument(
            'ticket',
            InputArgument::OPTIONAL,
            'Show tracked times for a certain ticket'
            )
            ->addOption(
                's',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
                )
            ->addOption(
                'c',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
                )
            ->addOption(
                'p',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
                )
            ->addOption(
                'f',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task will yell in uppercase letters'
                )
            ->addOption(
                't',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task will yell in uppercase letters'
                )
                ;
    }

    /**
     * Execute the command
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get the ticket
        $ticket = $input->getArgument('ticket');

        // filter by ticket if given
        $bookings = array();
        $storage = StorageFactory::getStorage();
        if ($ticket) {
            $bookings = $storage->retrieve($ticket);
        } else {
            $bookings = $storage->retrieveAll();
        }

        // format for output

        $output->write($text);
    }
}
