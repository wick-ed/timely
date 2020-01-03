<?php

/**
 * \Wicked\Timely\PushServices\Authentication\GenericPasswordRetrievalStrategy
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

namespace Wicked\Timely\PushServices\Authentication;

use JiraRestApi\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Date helper
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class UnixPasswordRetrievalStrategy implements PasswordRetrievalStrategyInterface
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * MacOsPasswordRetrievalStrategy constructor.
     *
     * @param OutputInterface $output
     * @param ConfigurationInterface $configuration
     */
    public function __construct(OutputInterface $output, ConfigurationInterface $configuration)
    {
        $this->output = $output;
        $this->configuration = $configuration;
    }

    /**
     * Prompt silent
     *
     * Interactively prompts for input without echoing to the terminal.
     * Requires a bash shell or Windows and won't work with
     * safe_mode settings (Uses `shell_exec`)
     *
     * Source: http://www.sitepoint.com/interactive-cli-password-prompt-in-php/
     *
     * @param OutputInterface $output Console output interface
     * @param string $prompt The message to the user
     *
     * @return string
     */
    protected function promptSilent(OutputInterface $output, $prompt = "Enter Password:")
    {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");
            return '';
        }
        $command = "/usr/bin/env bash -c 'read -s -p \""
            . addslashes($prompt)
            . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        $output->writeln('');
        return $password;
    }

    /**
     * Will retrieve a stored password from the OSX keychain
     *
     * @return string
     */
    public function getPassword()
    {
        $password = $this->promptSilent($this->output, 'Please enter password for your push service account "' . $this->configuration->getJiraUser() . '":');
        if (empty($password)) {
            $this->output->writeln('<error>Empty password is not possible...</error>');

            return '';
        }
        return $password;
    }
}
