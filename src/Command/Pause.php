<?php

/**
 * \Wicked\Timely\Command\Pause
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
use Wicked\Timely\Entities\Pause as PauseEntity;

/**
 * Class for the "pause" command. Command used pause current tracking
 *
 * @author    wick-ed
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Pause extends Command
{

    /**
     * Constant for the "resume" option
     *
     * @var string OPTION_RESUME
     */
    const OPTION_RESUME = 'resume';

    /**
     * Configures the "pause" command
     *
     * @return void
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('pause')
        ->setDescription('Pause current tracking')
        ->addArgument(
            'comment',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Comment why currently tracked task is paused'
        )
            ->addOption(
                self::OPTION_RESUME,
                substr(strtolower(self::OPTION_RESUME), 0, 1),
                InputOption::VALUE_NONE,
                'Will resume the task tracked before a pause has happened'
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
        try {
            // get the comment (if any)
            $comment = implode(' ', $input->getArgument('comment'));

            // check if we are resuming or pausing initially
            $resuming = false;
            $result = 'Pausing current tracking';
            if ($input->getOption(self::OPTION_RESUME)) {
                $this->assertConsistentResume();
                $resuming = true;
                $result = 'Resuming recent tracking';
            } else {
                $this->assertConsistentPause();
            }

            // create a new pause instance
            $pause = new PauseEntity($comment, $resuming);

            // get the configured storage instance and store the booking
            $storage = StorageFactory::getStorage();
            $storage->store($pause);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        // write output
        $output->writeln($result);
    }

    /**
     * Assert that a given "resume" command makes sense
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function assertConsistentResume()
    {
        $storage = StorageFactory::getStorage();
        $lastBooking = $storage->retrieveLast(true);
        if ($lastBooking->getTicketId() !== PauseEntity::PAUSE_TAG_START) {
            throw new \Exception('Cannot resume without an ongoing pause (forgot to start?)');
        }
    }

    /**
     * Assert that a given "pause" command makes sense
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function assertConsistentPause()
    {
        $storage = StorageFactory::getStorage();
        $lastBooking = $storage->retrieveLast(true);
        if ($lastBooking->getTicketId() === PauseEntity::PAUSE_TAG_START) {
            throw new \Exception('There already seems to be an ongoing pause');
        }
    }
}
