<?php

namespace ArthurZanella\Wirecard\Providers;

use ArthurZanella\Wirecard\Models\Wirecard;
use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Wirecard::class,
    ];
}