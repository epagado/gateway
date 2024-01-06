<?php

namespace Epagado\Facades;

use Illuminate\Support\Facades\Facade;

class Epagado extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'gateway'; }

}