<?php
/**
 * \Wicked\Timely\DotEnvConfiguration
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

namespace Wicked\Timely;

use JiraRestApi\Configuration\DotEnvConfiguration as JiraDotEnvConfiguration;

/**
 * DotEnvConfiguration class file
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class DotEnvConfiguration extends JiraDotEnvConfiguration
{

    protected $pushService;

    /**
     * DotEnvConfiguration constructor.
     *
     * @param string $path Path to the configuration file
     *
     * @throws \JiraRestApi\JiraException
     */
    public function __construct($path = '.')
    {
        parent::__construct(realpath(__DIR__ . '/../'));
        $this->pushService = $this->env('PUSH_SERVICE');
    }

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if ($this->startsWith($value, '"') && $this->endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function getPushService()
    {
        return $this->pushService;
    }

    /**
     * Setter for the jira password configuration
     *
     * @param string $value Password value to set
     *
     * @return void
     */
    public function setJiraPassword($value)
    {
        $this->jiraPassword = $value;
    }
}
