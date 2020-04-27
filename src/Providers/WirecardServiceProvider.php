<?php

namespace ArthurZanella\Wirecard\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class WirecardServiceProvider
 * @package ArthurZanella\Wirecard\Providers
 */
class WirecardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../Http/routes.php';

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'wirecard');

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );
    }
}
