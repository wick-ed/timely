<?php

/**
 * \Wicked\Timely\Command\SelfCheck
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

namespace Wicked\Timely\Command;

use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Packagist\Api\Client as PackagistClient;

/**
 * Class for the "selfcheck" command. Command is used to track time bookings
 *
 * @author    wick-ed
 * @copyright 2020 Bernhard Wick
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wick-ed/timely
 */
class SelfCheck extends Command
{

    /**
     * Constants used within this command
     *
     * @var string
     */
    const COMMAND_NAME = 'self-check';
    const PACKAGE_VENDOR = 'wick-ed';

    /**
     * Configures the "track" command
     *
     * @return void
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName(static::COMMAND_NAME)
        ->setDescription('Some self checks.')
        ->setHelp(<<<'EOF'
The <info>%command.name%</info> command does some self checks.
Including checking for the latest version.

An example call would be:

  <info>timely %command.name%</info>
EOF
            )
        ;
    }

    /**
     * Execute the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  The command input
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output
     *
     * @return int
     *
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $client = new PackagistClient();
            $package = $client->get(static::PACKAGE_VENDOR . '/' . strtolower($this->getApplication()->getName()));
        } catch (ConnectException $e) {
            $output->writeln('<comment>Could not check for available new versions. Are you offline?.</comment>');
            return 1;
        }
        $versions = array_keys($package->getVersions());
        $latestVersion = array_shift($versions);
        foreach ($versions as $version) {
            if (version_compare($latestVersion, $version, 'lt')) {
                $latestVersion = $version;
            }
        }

        if ($this->getApplication()->getVersion() === $latestVersion) {
            $output->writeln(sprintf('<comment>Your local installation is the latest available version (%s)</comment>', $latestVersion));
            return 0;
        }
        if (version_compare($latestVersion, $this->getApplication()->getVersion(), 'lt')) {
            $output->writeln(
                sprintf(
                    '<comment>Your local version %s exceeds available versions! You from the future? 0_o</comment>',
                    $this->getApplication()->getVersion()
                )
            );
            return 0;
        }
        $output->writeln(
            sprintf(
                '<error>Your local installation\'s version (%s) does differ from the latest available version %s. Please consider to update.</error>',
                $this->getApplication()->getVersion(),
                $latestVersion
            )
        );
        return 1;
    }
}
