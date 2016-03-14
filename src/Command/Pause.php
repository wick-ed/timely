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
use Wicked\Timely\Entities\Pause as PauseEntity;

/**
 * Class for the "pause" command. Command used pause current tracking
 *
 * @author    Bernhard Wick <wick.b@hotmail.de>
 * @copyright 2016 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Pause extends Command
{

    /**
     * Configures the "pause" command
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
            InputArgument::OPTIONAL,
            'Comment why currently tracked task is paused'
            )
            ->addOption(
                'r',
                null,
                InputOption::VALUE_NONE,
                'Will resume the task tracked before a pause has happened'
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
        // get the comment (if any)
        $comment = $input->getArgument('comment');

        // check if we are resuming or pausing initially
        $resuming = false;
        $result = 'Pausing current tracking';
        if ($input->getOption('r')) {
            $resuming = true;
            $result = 'Resuming recent tracking';
        }

        try {
            // create a new pause instance
            $pause = new PauseEntity($comment, $resuming);

            // get the configured storage instance and store the booking
            $storage = StorageFactory::getStorage();
            $storage->store($pause);

        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        // write output
        $output->writeln($result);
    }
}
