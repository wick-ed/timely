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
    const WORKLOG_TEMPO_FORMAT = 'tempo';
    /**
     * @var IssueService
     */
    protected $issueService;

    /**
     * @var PasswordRetrievalStrategyInterface
     */
    protected $passwordRetrievalStrategy;

    /**
     * @var TempoWorklogService
     */
    protected $tempoWorklogService;

    /**
     * @var string
     */
    private $configuration;

    /**
     * Jira constructor.
     *
     * @param PasswordRetrievalStrategyInterface $passwordRetrievalStrategy
     *
     * @throws \JiraRestApi\JiraException
     */
    public function __construct(PasswordRetrievalStrategyInterface $passwordRetrievalStrategy, DotEnvConfiguration $configuration)
    {
        $this->passwordRetrievalStrategy = $passwordRetrievalStrategy;
        $this->configuration = $configuration;
        $this->init();
    }

    /**
     * Initialize
     *
     * @throws \JiraRestApi\JiraException
     */
    protected function init()
    {
        $password = $this->configuration->getJiraPassword();
        if (empty($password)) {
            $password = $this->passwordRetrievalStrategy->getPassword();
            //$password = $this->getPasswordFromKeychain($this->configuration);
            if (empty($password)) {
                return;
            }
            $this->configuration->setJiraPassword($password);
        }
        if ($this->configuration->getWorklogFormat() === self::WORKLOG_TEMPO_FORMAT) {
            $this->tempoWorklogService = new TempoWorklogService($this->configuration);
            $this->tempoWorklogService->setAPIUri('/rest/tempo-timesheets/4');
        }
        $this->issueService = new IssueService($this->configuration);
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
        $timeSpend = Date::roundByInterval($task->getDuration(), 900);
        $comment = $task->getComment();

        // check the sanity of our worklog and discard it if there is something missing
        if (!$task->getTicketId() || empty($timeSpend) || empty($comment)) {
            throw new \Exception('Not pushing one worklog as it misses vital information');
        }

        if ($this->configuration->getWorklogFormat() === self::WORKLOG_TEMPO_FORMAT) {
            // Update Starttime in Jira. Tempo has only date, not time
            $tempoWorklogResult = $this->createTempoWorklog($task, $timeSpend);

            // Update Starttime in Jira. Tempo has only date, not time
            $workLog = new Worklog();
            $workLog->setStarted($task->getStartTime());
            // push to remote
            $this->issueService->editWorklog($task->getTicketId(), $workLog, $tempoWorklogResult->originId);
        } else {
            $workLog = new Worklog();
            $workLog->setComment($comment)
                ->setStarted($task->getStartTime())
                ->setTimeSpent(Date::secondsToUnits($timeSpend));
            // push to remote
            $this->issueService->addWorklog($task->getTicketId(), $workLog);
        }
        return true;
    }

    /**
     * @param Task $task
     * @param $timeSpend
     * @return TempoWorklog
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     */
    protected function createTempoWorklog(Task $task, $timeSpend)
    {
        $tempoWorklog = new TempoWorklog();
        $tempoWorklog->setComment($task->getComment())
            ->setStarted($task->getStartTime())
            ->setTimeSpentSeconds($timeSpend)
            ->setBillableSeconds($this->getBillableBlacklist($task->getTicketId(), $timeSpend))
            ->setOriginTaskId($task->getTicketId())
            ->setWorker($this->configuration->getJiraUser())
            ->setAttributes($this->generateAttributes('_Account_', ''));
        // push to remote
        $tempoWorklogResult = $this->tempoWorklogService->addWorklog($tempoWorklog);
        // Check if account Attribute to set
        if (!empty($tempoWorklogResult->issue->accountKey)) {
            $tempoWorklog->setAttributes($this->generateAttributes('_Account_', $tempoWorklogResult->issue->accountKey));
            // push change to remote
            $tempoWorklogResult = $this->tempoWorklogService->editWorklog(
                $tempoWorklog,
                $tempoWorklogResult->tempoWorklogId
            );
        }
        return $tempoWorklogResult;
    }

    /**
     * @param $key
     * @param $value
     * @return array[]
     */
    protected function generateAttributes($key, $value): array
    {
        return [
            $key => [
                'key' => $key,
                'value' => $value
            ]
        ];
    }

    /**
     * @param string $issue
     * @param int $seconds
     * @return int
     */
    protected function getBillableBlacklist($issue, $seconds) {
        foreach($this->configuration->getTempoBlacklistBillable() as $blacklistPattern) {
            if (empty(trim($blacklistPattern))) {
                continue;
            }
            $matches = [];
            preg_match($blacklistPattern, $issue, $matches);
            if (count($matches) === 1) {
                return 0;
            }
        }

        return $seconds;
    }
}
