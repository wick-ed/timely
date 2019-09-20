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
 * @author    wick-ed
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
 * @author    wick-ed
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Show extends AbstractReadCommand
{

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
        ->setDescription('Show tracked times');

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

        // prepare with empty message
        $text = 'Sorry, could not find any matching bookings';

        // format for output (if we got bookings)
        if (!empty($bookings)) {
            $formatter = FormatterFactory::getFormatter(FormatterFactory::OUTPUT_CHANNEL);
            $text = $formatter->toString($bookings);
        }

        // write output
        $output->write($text, true);
    }
}
