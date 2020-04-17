<?php

/**
 * \Wicked\Timely\Helper\SelfCheck
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

namespace Wicked\Timely\Helper;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use \Wicked\Timely\Command\SelfCheck as SelfCheckCommand;
use Wicked\Timely\Storage\StorageFactory;

/**
 * SelfCheck helper
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class SelfCheck
{

    /**
     * Constants being needed
     */
    const AUTO_SELF_CHECK_TIMESTAMP_FILE = 'last_auto_selfcheck_timestamp';
    const AUTO_SELF_CHECK_DELAY = 24*60*60;

    /**
     * Makes an auto-self check but buffers for a certain amount of time
     *
     * @param Application $application
     *
     * @return void
     *
     * @throws \Exception
     */
    public static function autoCheck(Application $application)
    {
        $storage = StorageFactory::getStorage();
        $lastCheckTimestampFilepath = dirname($storage->getLogFilePath()) .
            DIRECTORY_SEPARATOR .
            static::AUTO_SELF_CHECK_TIMESTAMP_FILE;
        $lastCheckTimestamp = (int) file_get_contents($lastCheckTimestampFilepath);
        if ($lastCheckTimestamp < time() - static::AUTO_SELF_CHECK_DELAY) {
            $command = $application->find(SelfCheckCommand::COMMAND_NAME);
            $input = new ArrayInput([]);
            $output = new ConsoleOutput();
            $command->run($input, $output);
            file_put_contents($lastCheckTimestampFilepath, time());
        }
    }
}
