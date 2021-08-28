<?php

namespace Pharaonic\Livewire\Table;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/options.php', 'livewire-table');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // VIEWS
        $this->loadViewsFrom(__DIR__ . '/../resources/views/components', 'livewire-table');

        // COMPONENTS
        Livewire::component('livewire-row', RowComponent::class);

        // PUBLISHING
        $this->publishes([
            __DIR__ . '/../resources/views/components'  => resource_path('views/vendor/livewire-table'),
            __DIR__.'/../config/options.php'            => config_path('Pharaonic/Livewire/table.php'),
        ], ['pharaonic', 'livewire-table']);
    }
}
