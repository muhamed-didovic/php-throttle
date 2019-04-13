<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | This defines the cache driver to be used. It may be the name of any
    | driver set in config/cache.php. Setting it to null will use the driver
    | you have set as default in config/cache.php.
    |
    | Default: null
    |
    */
    
    'driver'       => 'file',
    //todo: add desc
    'cache.path'   => '/tmp',
    'cache.driver' => 'file',
    'limit'        => 10,
    'time'         => 1,
    
    // place to store routes that we want to use throttle on
    'routes'       => [
    
    ],

];
