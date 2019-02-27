PHP Throttle
============

PHP/Zend Throttle was created by, and is maintained by [Muhamed Didovic](https://github.com/muhamed-didovic) and [Goran Radosevic](https://github.com/gradosevic), 
and is a rate limiter for Zend.

## Installation

PHP/Zend Throttle requires [PHP](https://php.net) 5.5 and up

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require muhamed-didovic/throttle
```

## Usage

```
$throttle = new MuhamedDidovic\Throttle\Throttle();
$routes = [
      [
          'url' => '/signup',
          'limit' => 10,
          'time' => 2
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
];
//Time is in minutes, the default number of attempts 100/1 minute
if (!$throttle->attempt($request, (int) 100, (int) $time = 1, $routes)) {
    echo "Rate limit exceeded. Please wait ".$time * 60 . " sec."; die;
}
```
