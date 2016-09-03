<?php

namespace App\Providers;

use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\IAggregateRepository;
use App\Infrastructure\Database\IRedisDB;
use App\Infrastructure\Database\RedisDB;
use App\Infrastructure\Event\EventStore\EloquentEventStore;
use App\Infrastructure\Event\EventStore\IEventStore;
use App\Infrastructure\Event\IEventBus;
use App\Infrastructure\Event\LaravelEventBus;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            IRedisDB::class,
            function ($app) {
                return new RedisDB($app->make('redis'));
            }
        );
        $this->app->bind(
            IAggregateRepository::class,
            AggregateRepository::class
        );


        $this->app->bind(
            IEventStore::class,
            EloquentEventStore::class
        );

        $this->app->bind(
            IEventBus::class,
            LaravelEventBus::class
        );
    }
}
