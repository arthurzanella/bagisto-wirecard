<?php

namespace ArthurZanella\Wirecard\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 * @package ArthurZanella\Wirecard\Providers
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       
        // Admin
        Event::listen('sales.order.payment-method.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('wirecard::admin.sales.orders.payment-status');
        });

    }
}
