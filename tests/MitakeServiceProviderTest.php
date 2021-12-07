<?php

namespace TaiwanSms\Mitake\Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use TaiwanSms\Mitake\Client;
use TaiwanSms\Mitake\MitakeServiceProvider;

class MitakeServiceProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRegister()
    {
        if (PHP_VERSION_ID < 50600 === true) {
            $this->markTestSkipped('PHP VERSION must bigger then 5.6');
        }

        $config = new Repository();
        $config->set('services.mitake', ['username' => 'foo', 'password' => 'bar']);
        $app = m::mock(new Container());
        $app->instance('config', $config);

        $serviceProvider = new MitakeServiceProvider($app);

        $app->expects('singleton')->with('TaiwanSms\Mitake\Client', m::on(function ($closure) use ($app) {
            return $closure($app) instanceof Client;
        }));

        $serviceProvider->register();
    }
}
