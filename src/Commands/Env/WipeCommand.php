<?php

namespace Pantheon\Terminus\Commands\Env;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class WipeCommand
 * Testing class for Pantheon\Terminus\Commands\Env\WipeCommand
 * @package Pantheon\Terminus\Commands\Env
 */
class WipeCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Deletes all files and database content in the environment.
     *
     * @authorize
     *
     * @command env:wipe
     *
     * @param string $site_env Site & environment in the format `site-name.env`
     *
     * @throws TerminusException
     * @usage terminus env:wipe <site>.<env>
     *    Deletes all database/files on <site>'s <env> environment.
     */
    public function wipe($site_env)
    {
        list($site, $env) = $this->getSiteEnv($site_env);
        $workflow = $env->wipe();
        $this->log()->notice(
            'Wiping the "{env}" environment of "{site}"',
            ['site' => $site->get('name'), 'env' => $env->id,]
        );
        while (!$workflow->checkProgress()) {
            // @TODO: Add Symfony progress bar to indicate that something is happening.
        }
        $this->log()->notice($workflow->getMessage());
    }
}
