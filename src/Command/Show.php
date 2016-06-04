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
     * Constant for the "yesterday" keyword
     *
     * @var string FILTER_KEYWORD_YESTERDAY
     */
    const FILTER_KEYWORD_YESTERDAY = 'yesterday';

    /**
     * Constant for the "current" keyword
     *
     * @var string FILTER_KEYWORD_CURRENT
     */
    const FILTER_KEYWORD_CURRENT = 'current';

    /**
     * Constant for the "to" option
     *
     * @var string OPTION_TO
     */
    const OPTION_TO = 'to';

    /**
     * Constant for the "from" option
     *
     * @var string OPTION_FROM
     */
    const OPTION_FROM = 'from';

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
                self::OPTION_FROM,
                null,
                InputOption::VALUE_REQUIRED,
                'Show from a certain date on'
            )
            ->addOption(
                self::OPTION_TO,
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

        // we might need framing dates
        $toDate = null;
        $fromDate = null;

        // check if we got a keyword
        if ($ticket === self::FILTER_KEYWORD_TODAY) {
            // set the fromDate to today, and clear the ticket
            $fromDate = strtotime(date('Y-m-d', time()));
            $ticket = null;

        } elseif ($ticket === self::FILTER_KEYWORD_YESTERDAY) {
            // set the fromDate to yesterday, the toDate to today and clear the ticket
            $fromDate = strtotime(date('Y-m-d', time() - 24 * 60 * 60));
            $toDate = strtotime(date('Y-m-d', time()));
            $ticket = null;

        } else {
            // check for options first
            if ($input->getOption(self::OPTION_TO)) {
                // test for valid format
                $tmpDate = strtotime($input->getOption(self::OPTION_TO));
                if ($tmpDate !== false) {
                    $toDate = $tmpDate;
                }
            }
            if ($input->getOption(self::OPTION_FROM)) {
                // test for valid format
                $tmpDate = strtotime($input->getOption(self::OPTION_FROM));
                if ($tmpDate !== false) {
                    $fromDate = $tmpDate;
                }
            }
        }

        // check for an amount limiting keyword
        $limit = null;
        if ($ticket === self::FILTER_KEYWORD_CURRENT) {
            // set the limit to 1, and clear the ticket
            $limit = 1;
            $ticket = null;
        }

        // filter by ticket if given
        /** @var \Wicked\Timely\Storage\StorageFactory $storage */
        $storage = StorageFactory::getStorage();
        $bookings = $storage->retrieve($ticket, $toDate, $fromDate, $limit);

        // format for output
        $formatter = FormatterFactory::getFormatter(FormatterFactory::OUTPUT_CHANNEL);
        $text = $formatter->toString($bookings);

        // write output
        $output->write($text, true);
    }
}
