<?php

namespace App\Providers;


use App\Contracts\JsonRpcServerInterface;
use App\Services\DemoJsonRpcServer;
use Illuminate\Support\ServiceProvider;



/**
 * Регистрация используемого на сайте JSON-RPC сервера,
 * работающего с конкретной библиотекой.
 *
 * Class JsonRpcServerServiceProvider
 * @package App\Providers
 */
class JsonRpcServerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // todo требуется замена на полноценную серверную библиотеку под Laravel 8
        $this->app->bind(JsonRpcServerInterface::class, function ($app) { return new DemoJsonRpcServer(); });
    }
}
