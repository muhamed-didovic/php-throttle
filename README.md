PHP Throttle
============

PHP Throttle is a rate limiter for Zend / Laravel or PHP and was made and maintained by [Muhamed Didovic](https://github.com/muhamed-didovic) and [Goran Radosevic](https://github.com/gradosevic).

## Installation

PHP Throttle requires [PHP](https://php.net) 5.5 and up

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require muhamed-didovic/throttle
```

## Usage

```
$config = [
    'cache.path' => '/tmp',
    'cache.driver' => 'file',
    'limit' => 10,
    'time' => 1,
    'routes' => [
        [
            'url' => '/signup',
            'limit' => 10
        ],
        [
            'url' => '/signin',
            'limit' => 3,
        ],
        [
            'url' => '/signin',
            'limit' => 2,
            'method' => 'POST'
        ]
    ]
];

$throttle = (new ThrottleApp($config))->getThrottle();

if (!$throttle->attempt($request)) {
    echo "Rate limit exceeded. Please wait " . 60 . " sec."; die;
}
```

Please note that $request can be different for different systems

### ZEND
 - $request is an instance of  Zend_Controller_Request_Http

### LARAVEL
 - $request is na instance of Illuminate\Http\Request

### Array
 - $request is an array
```
$request = [
   'ip' => '127.0.0.1',
   'method' => 'POST',
   'route' => '/example-page'
];
```

### Object
 - $request is an object
```
$request = (object)[
   'ip' => '127.0.0.1',
   'method' => 'POST',
   'route' => '/example-page'
];
```

## Configuration Parameters
All configuration parameters are optional. Add your own to override the defaults.

### cache.path
Path to the cache folder. "/tmp" by default

### cache.driver
Currently we support only "file" driver type

### limit
The number of allowed hits in a period of time. Default: 100

### time
A period of time, in minutes where we check for attempts. Default 1, minute.

### routes
Add here specific routes to check. All other routes will not be used against throttle.
If you don't provide this parameter, all routes will be checked against throttle
