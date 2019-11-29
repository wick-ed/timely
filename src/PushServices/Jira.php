<?php

/**
 * \Wicked\Timely\PushServices\Jira
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

namespace Wicked\Timely\PushServices;

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Worklog;
use Wicked\Timely\DotEnvConfiguration;
use Wicked\Timely\Entities\Task;
use Wicked\Timely\Helper\Date;
use Wicked\Timely\PushServices\Authentication\PasswordRetrievalStrategyInterface;

/**
 * Jira push service
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class Jira implements PushServiceInterface, AuthenticationAwarePushServiceInterface
{

    /**
     * @var IssueService
     */
    protected $issueService;

    /**
     * @var PasswordRetrievalStrategyInterface
     */
    protected $passwordRetrievalStrategy;

    /**
     * Jira constructor.
     *
     * @param PasswordRetrievalStrategyInterface $passwordRetrievalStrategy
     *
     * @throws \JiraRestApi\JiraException
     */
    public function __construct(PasswordRetrievalStrategyInterface $passwordRetrievalStrategy)
    {
        $this->passwordRetrievalStrategy = $passwordRetrievalStrategy;
        $this->init();
    }

    /**
     * Initialize
     *
     * @throws \JiraRestApi\JiraException
     */
    protected function init()
    {
        // retrieve configuration
        $configuration = new DotEnvConfiguration();
        $password = $configuration->getJiraPassword();
        if (empty($password)) {
            $password = $this->passwordRetrievalStrategy->getPassword();
            //$password = $this->getPasswordFromKeychain($configuration);
            if (empty($password)) {
                return;
            }
            $configuration->setJiraPassword($password);
        }
        $this->issueService = new IssueService($configuration);
    }

    /**
     * Push a given task to a (remote) service
     *
     * @param Task $task
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function push(Task $task)
    {
        // create a worklog from the task
        $workLog = new Worklog();
        $workLog->setComment($task->getComment())
            ->setStarted($task->getStartTime())
            ->setTimeSpent(Date::secondsToUnits(Date::roundByInterval($task->getDuration(), 900)));

        // check the sanity of our worklog and discard it if there is something missing
        if (!$task->getTicketId() || empty($workLog->timeSpent) || empty($workLog->comment)) {
            throw new \Exception('Not pushing one worklog as it misses vital information');
        }

        // push to remote
        $this->issueService->addWorklog($task->getTicketId(), $workLog);
        return true;
    }
}
