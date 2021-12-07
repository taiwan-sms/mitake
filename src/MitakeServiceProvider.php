<?php

namespace TaiwanSms\Mitake;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MitakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = Arr::get($app['config'], 'services.mitake', []);

            return new Client(Arr::get($config, 'username'), Arr::get($config, 'password'));
        });
    }
}
