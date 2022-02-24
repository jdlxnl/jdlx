<?php

namespace Jdlx;

use Illuminate\Support\ServiceProvider;
use Jdlx\Commands\Account\AccountChangePasswordCommand;
use Jdlx\Commands\Account\AccountCreateCommand;
use Jdlx\Commands\Auth\LoginSanctumCommand;
use Jdlx\Commands\Docs\GenerateDocsCommand;
use Jdlx\Commands\Account\AccountAssignRoleCommand;
use Jdlx\Commands\Jdlx\JdlxCreateAdminCommand;
use Jdlx\Commands\Jdlx\JdlxInstallCommand;


class JdlxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateDocsCommand::class,
                AccountChangePasswordCommand::class,
                AccountCreateCommand::class,
                LoginSanctumCommand::class,
                AccountCreateCommand::class,
                AccountAssignRoleCommand::class,
                JdlxInstallCommand::class,
                JdlxCreateAdminCommand::class,
            ]);
        }


        $this->publishes([
            __DIR__ . '/../publish/app' => base_path('app'),
            __DIR__ . '/../publish/routes' => base_path('routes'),
            __DIR__ . '/../publish/tests' => base_path('tests'),
            __DIR__ . '/../publish/config' => base_path('config'),
            __DIR__ . '/../publish/database/seeders' => base_path('database/seeders'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'jdlx');
        $this->loadRoutesFrom(__DIR__ . '/../routes/docs.php');

    }


}
