<?php
/**
 * Created by PhpStorm.
 * User: balazss
 * Date: 5/4/2017
 * Time: 10:24 AM
 */

namespace LavueCms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LavueCmsServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        $this->publishes([__DIR__ . '/database/migrations' => database_path('migrations')]
            , 'migrations');

        $this->register();
    }

    public function register()
    {
        $this->registerCommands();
    }

    public function registerCommands()
    {

    }
}