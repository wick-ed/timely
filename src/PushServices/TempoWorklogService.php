<?php

namespace Wicked\Timely\PushServices;

use JiraRestApi\JiraException;

class TempoWorklogService extends \JiraRestApi\JiraClient
{
    private $uri = '/worklogs';

    /**
     * add work log to issue.
     *
     * @param TempoWorklog|object $worklog
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return TempoWorklog Worklog Object
     */
    public function addWorklog(TempoWorklog $worklog)
    {

        $data = json_encode($worklog);
        $this->log->info("addWorklog=\n", [ 'data' => $data]);
        $type = 'POST';

        $ret = $this->exec($this->uri, $data, $type);

        $ret_worklog = $this->json_mapper->map(
            json_decode($ret)[0],
            new TempoWorklog()
        );

        return $ret_worklog;
    }

    /**
     * edit the worklog.
     *
     * @param TempoWorklog|object $worklog
     * @param string|int     $tempoWorklogId
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Worklog
     */
    public function editWorklog($worklog, $tempoWorklogId)
    {
        $data = json_encode($worklog);
        $this->log->info("editWorklog=\n", [ 'data' => $data]);

        $url = $this->uri."/$tempoWorklogId";
        $type = 'PUT';

        $ret = $this->exec($url, $data, $type);

        $ret_worklog = $this->json_mapper->map(
            json_decode($ret),
            new TempoWorklog()
        );

        return $ret_worklog;
    }
}
