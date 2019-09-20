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
use Wicked\Timely\PushServices\Authentication\MacOsPasswordRetrievalStrategy;

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
        $pushService = new Jira();

        if ($pushService instanceof AuthenticationAwarePushServiceInterface)
        {
            // TODO make the strategy configurable and into the real pattern
            $passwordRetrievalStrategy = new MacOsPasswordRetrievalStrategy($output);
            $pushService->injectPasswortRetrievalStrategy($passwordRetrievalStrategy);
        }

        return $pushService;
    }
}
