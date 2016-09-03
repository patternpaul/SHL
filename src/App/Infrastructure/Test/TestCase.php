<?php
namespace App\Infrastructure\Test;

use App\Infrastructure\AggregateFactory;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {

        putenv('SESSION_DRIVER=file');
        putenv('QUEUE_DRIVER=null');

        $app = require __DIR__.'/../../../bootstrap/app.php';
        AggregateFactory::destroy();

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->singleton(
            \App\Infrastructure\Event\EventStore\IEventStore::class,
            \App\Infrastructure\Event\EventStore\InMemoryEventStore::class
        );

        $app->singleton(
            \App\Infrastructure\Database\IRedisDB::class,
            \App\Infrastructure\Database\InMemoryRedisDB::class
        );

        $app->singleton(
            \App\Auth\IUserRepo::class,
            \App\Auth\TestMockUserRepo::class
        );
        $app->singleton(
            \App\Infrastructure\Util\Encrypt\IKeyRepo::class,
            \App\Infrastructure\Util\Encrypt\MockKeyRepo::class
        );

        $app->singleton(
            \App\Infrastructure\OpenStack\IOpenStack::class,
            \App\Infrastructure\OpenStack\TestOpenStack::class
        );

        return $app;
    }
    public function dispatch($job)
    {
        return \Illuminate\Support\Facades\Bus::dispatch($job);
        //return $this->app[Illuminate\Contracts\Bus\Dispatcher::class]->dispatch($job);
    }
}
