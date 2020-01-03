<?php

/**
 * \Wicked\Timely\PushServices\Authentication\MacOsPasswordRetrievalStrategy
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

/**
 * Date helper
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class MacOsPasswordRetrievalStrategy extends UnixPasswordRetrievalStrategy
{
    /**
     * Constants used within this class
     *
     * @var string
     */
    const KEYCHAIN_SAVE = 'timely jira access';

    /**
     * Will retrieve a stored password from the OSX keychain
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getPassword()
    {
        $password = rtrim(shell_exec("security find-generic-password -s '".self::KEYCHAIN_SAVE."' -w"));
        if (empty($password)) {
            $password = parent::getPassword();
            if (empty($password)) {
                throw new \Exception(sprintf('Could not retrieve password.'));
            }
            shell_exec('security add-generic-password -a "'.$this->configuration->getJiraUser().'" -s "'.self::KEYCHAIN_SAVE.'" -w "'.$password.'"');
        }
        return $password;
    }
}
