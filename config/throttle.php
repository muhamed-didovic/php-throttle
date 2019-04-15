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
    //same as above
    'cache.driver' => 'file',
    //path where the files will be stored
    'cache.path'   => '/tmp',
    //default limit per route
    'limit'        => 10,
    //default time in minutes per route
    'time'         => 1,
    // place to store routes that we want to use throttle on
    'routes'       => [
    
    ],

];
