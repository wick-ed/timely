<?php

/**
 * \Wicked\Timely\PushServices\PushServiceFactory
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
 * @copyright 2019 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */

namespace Wicked\Timely\PushServices;

use Symfony\Component\Console\Output\OutputInterface;
use Wicked\Timely\DotEnvConfiguration;
use Wicked\Timely\PushServices\Authentication\MacOsPasswordRetrievalStrategy;
use Wicked\Timely\PushServices\Authentication\UnixPasswordRetrievalStrategy;

/**
 * Push Service factory
 *
 * @author    wick-ed
 * @copyright 2019 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class PushServiceFactory
{

    /**
     * Get a push service as configured
     *
     * @return PushServiceInterface
     *
     * @throws \JiraRestApi\JiraException
     */
    public static function getService(OutputInterface $output)
    {
        $configuration = new DotEnvConfiguration();

        $passwordRetrievalStrategy = new UnixPasswordRetrievalStrategy($output, $configuration);
        if (static::getOs() === 'Darwin') {
            $passwordRetrievalStrategy = new MacOsPasswordRetrievalStrategy($output, $configuration);
        }

        $pushService = $configuration->getPushService();
        switch ($pushService) {
            case 'jira':
                $pushService = new Jira($passwordRetrievalStrategy);
                break;

            default:
                throw new \Exception(sprintf('Cannot create push service instance for "%s" configuration value', $pushService));
                break;
        }

        return $pushService;
    }

    /**
     * Detect the host OS.
     * Returns any of 'Windows', 'Darwin', 'Linux' or 'Unknown'.
     * Intentionally kept the same as PHP_OS_FAMILY to be substituted when PHP < 7.2 isn't a thing anymore.
     * @see https://www.php.net/manual/en/reserved.constants.php#constant.php-os-family
     *
     * @return string
     */
    protected static function getOs()
    {
        $osString = php_uname('s');
        if (strpos($osString, 'Darwin') === 0) {
            return 'Darwin';
        }
        if (strpos($osString, 'Windows') === 0) {
            return 'Windows';
        }
        if (strpos($osString, 'Linux') === 0) {
            return 'Linux';
        }
        return 'Unknown';
    }
}
