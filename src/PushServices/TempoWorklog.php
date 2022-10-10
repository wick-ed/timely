<?php

namespace Wicked\Timely\PushServices;

use JiraRestApi\ClassSerialize;
use JiraRestApi\JiraException;

/**
 * Class TempoWorklog.
 */
class TempoWorklog
{
    use ClassSerialize;

    /**
     * @var string User key of user logging work
     */
    public $worker;

    /**
     * @var array|null of attributes
     */
    public $attributes  = null;

    /**
     * @var mixed
     *
     */
    public $comment;

    /**
     * Issue Key or ID
     * @var string
     */
    public $originTaskId;

    /**
     * @var string
     */
    public $started;

    /**
     * @var int
     */
    public $timeSpentSeconds;

    /**
     * @var int
     */
    public $billableSeconds;


    /**
     * Function to serialize obj vars.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param int $billableSeconds
     * @return TempoWorklog
     */
    public function setBillableSeconds(int $billableSeconds)
    {
        $this->billableSeconds = $billableSeconds;
        return $this;
    }


    /**
     * @param string $worker
     * @return TempoWorklog
     */
    public function setWorker(string $worker)
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * @param string $originTaskId
     * @return TempoWorklog
     */
    public function setOriginTaskId(string $originTaskId)
    {
        $this->originTaskId = $originTaskId;
        return $this;
    }

    /**
     * Function to set comments.
     *
     * @param mixed $comment
     *
     * @return TempoWorklog
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }


    /**
     * Function to set start time of worklog.
     *
     * @param mixed $started
     *
     * @throws JiraException
     *
     * @return TempoWorklog
     */
    public function setStarted($started)
    {
        if (is_string($started)) {
            $dt = new \DateTime($started);
        } elseif ($started instanceof \DateTimeInterface) {
            $dt = $started;
        } else {
            throw new JiraException('field only accept date string or DateTimeInterface object.'.get_class($started));
        }

        $this->started = $dt->format("Y-m-d");
        return $this;
    }


    /**
     * Function to set worklog time in seconds.
     *
     * @param int $timeSpentSeconds
     *
     * @return TempoWorklog
     */
    public function setTimeSpentSeconds($timeSpentSeconds)
    {
        $this->timeSpentSeconds = $timeSpentSeconds;

        return $this;
    }
}
