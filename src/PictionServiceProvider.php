<?php

namespace Braid\Piction;

use Illuminate\Support\ServiceProvider;
use Braid\Piction\Commands\PictionIngest;
use Braid\Piction\Commands\PictionDeleted;
use Braid\Piction\Observers\RecordObserver;
use Braid\Piction\Commands\PictionCollections;
use Braid\Piction\Observers\ScoutRecordObserver;

class PictionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/piction.php' => config_path('piction.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PictionIngest::class,
                PictionDeleted::class,
                PictionCollections::class,
            ]);
        }

        if (config('piction.use_scout')) {
            \Braid\Piction\Models\Scout\Record::observe(ScoutRecordObserver::class);
        } else {
            \Braid\Piction\Models\Record::observe(RecordObserver::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('braid-piction', function () {
            return new Piction();
        });
    }
}
