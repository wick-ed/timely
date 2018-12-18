<?php
/**
 * Created by PhpStorm.
 * User: wickb
 * Date: 28.11.18
 * Time: 08:10
 */

namespace Wicked\Timely;

use JiraRestApi\Configuration\DotEnvConfiguration as JiraDotEnvConfiguration;

class DotEnvConfiguration extends JiraDotEnvConfiguration
{

    /**
     * @param string $path
     */
    public function __construct($path = '.')
    {
        parent::__construct(realpath(__DIR__ . '/../'));
    }
}
