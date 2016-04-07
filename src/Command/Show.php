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
use Wicked\Timely\Formatter\FormatterFactory;

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
     * Constant for the "today" keyword
     *
     * @var string FILTER_KEYWORD_TODAY
     */
    const FILTER_KEYWORD_TODAY = 'today';

    /**
     * Configures the "show" command
     *
     * @return void
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
                'f',
                null,
                InputOption::VALUE_REQUIRED,
                'Show from a certain date on'
            )
            ->addOption(
                't',
                null,
                InputOption::VALUE_REQUIRED,
                'Show up to a certain date'
            );
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

        // check for options first
        $toDate = null;
        $fromDate = null;
        if ($input->getOption('t')) {
            // test for valid format
            $tmpDate = strtotime($input->getOption('t'));
            if ($tmpDate !== false) {
                $toDate = $tmpDate;
            }
        }
        if ($input->getOption('f')) {
        // test for valid format
            $tmpDate = strtotime($input->getOption('f'));
            if ($tmpDate !== false) {
                $fromDate = $tmpDate;
            }
        }

        // filter by ticket if given
        $storage = StorageFactory::getStorage();
        $bookings = $storage->retrieve($ticket, $toDate, $fromDate);

        // format for output
        $formatter = FormatterFactory::getFormatter(FormatterFactory::OUTPUT_CHANNEL);
        $text = $formatter->toString($bookings);

        // write output
        $output->write($text, true);
    }
}
