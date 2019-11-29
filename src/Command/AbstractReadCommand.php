<?php

/**
 * \Wicked\Timely\Command\AbstractReadCommand
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
abstract class AbstractReadCommand extends Command
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
     * Constant for the "interval" option
     *
     * @var string OPTION_SPECIFIC
     */
    const OPTION_SPECIFIC = 'interval';

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
        ->addArgument(
            'ticket',
            InputArgument::OPTIONAL,
            'Show tracked times for a certain ticket'
        )
            ->addOption(
                self::OPTION_FROM,
                substr(strtolower(self::OPTION_FROM), 0, 1),
                InputOption::VALUE_REQUIRED,
                'Show from a certain date on'
            )
            ->addOption(
                self::OPTION_SPECIFIC,
                substr(strtolower(self::OPTION_SPECIFIC), 0, 1),
                InputOption::VALUE_REQUIRED,
                'Show only for a specific date'
            )
            ->addOption(
                self::OPTION_TO,
                substr(strtolower(self::OPTION_TO), 0, 1),
                InputOption::VALUE_REQUIRED,
                'Show up to a certain date'
            );
    }

    /**
     * Will provide given input params in a usable format
     *
     * @param InputInterface  $input    The Symfony console input
     * @param string          $ticket   Ticket ID handle, if given
     * @param null|string|int $fromDate The date from which an action should be done
     * @param null|string|int $toDate   The date up to which an action should be done
     * @param int             $limit    Limit for the action, e.g. retrieving tasks
     *
     * @return void
     */
    protected function prepareInputParams(InputInterface $input, & $ticket, & $fromDate, & $toDate, & $limit)
    {
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
            if ($input->getOption(self::OPTION_SPECIFIC)) {
                // test for valid format
                $tmpDate = strtotime($input->getOption(self::OPTION_SPECIFIC));
                if ($tmpDate !== false) {
                    $fromDate = $tmpDate;
                    $toDate = $fromDate + 24 * 60 * 60;
                }
            } else {
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
        }

        // check for an amount limiting keyword
        if ($ticket === self::FILTER_KEYWORD_CURRENT) {
            // set the limit to 1, and clear the ticket
            $limit = 1;
            $ticket = null;
        }
    }
}
