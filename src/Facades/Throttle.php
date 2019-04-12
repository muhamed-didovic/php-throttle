<?php

namespace MuhamedDidovic\Throttle\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the throttle facade class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class Throttle extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'throttle';
    }
}
