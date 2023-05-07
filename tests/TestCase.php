<?php

namespace IgniterLabs\SmsNotify\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \IgniterLabs\SmsNotify\Extension::class,
        ];
    }
}
