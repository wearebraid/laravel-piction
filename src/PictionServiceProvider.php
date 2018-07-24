<?php

namespace Wearebraid\Piction;

use Illuminate\Support\ServiceProvider;
use Wearebraid\Piction\Commands\PictionIngest;
use Wearebraid\Piction\Commands\PictionDeleted;
use Wearebraid\Piction\Observers\RecordObserver;
use Wearebraid\Piction\Commands\PictionCollections;
use Wearebraid\Piction\Observers\ScoutRecordObserver;

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
            \Wearebraid\Piction\Models\Scout\Record::observe(ScoutRecordObserver::class);
        } else {
            \Wearebraid\Piction\Models\Record::observe(RecordObserver::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('wearebraid-piction', function () {
            return new Piction();
        });
    }
}
