<?php

//declare(strict_types=1);


namespace MuhamedDidovic\Throttle;

use MuhamedDidovic\Throttle\Factories\CacheFactory;
use MuhamedDidovic\Throttle\Transformers\ArrayTransformer;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Filesystem\Filesystem;

/**
 * This is the throttle class.
 */
class Throttle
{
    /**
     * The cached throttler instances.
     *
     * @var Throttlers\ThrottlerInterface[]
     */
    protected $throttlers = [];

    /**
     * The factory instance.
     *
     * @var \MuhamedDidovic\Throttle\Factories\FactoryInterface
     */
    protected $factory;

    /**
     * The factory instance.
     *
     * @var \MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface
     */
    protected $transformer;

    /**
     * Create a new instance.
     *
     * @param \MuhamedDidovic\Throttle\Factories\FactoryInterface               $factory
     * @param \MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface $transformer
     *
     * @return void
     */
    public function __construct()
    {
        $this->factory = new CacheFactory(new \Illuminate\Cache\Repository(new FileStore(new Filesystem, '/tmp')));
        $this->transformer = new TransformerFactory();
    }

    /**
     * Get a new throttler.
     *
     * @param mixed $data
     * @param int $limit
     * @param int $time
     *
     * @param null $routes
     * @return Throttlers\ThrottlerInterface
     * @internal param null $rules
     */
    public function get($data, $limit = 10, $time = 1, $routes = NULL)
    {
        if($routes){
            foreach($routes as $route){
                if(empty($route) || empty($route['url'])) continue;

                $routeUrl = $route['url'];
                $routeLimit = !empty($route['limit'])?$route['limit']: $limit;
                $routeTime = !empty($route['time'])?$route['time']:$time;
                $routeMethod = !empty($route['method'])? $route['method']: 'GET';

                if($routeUrl != $data->getPathInfo() || $routeMethod != $data->getMethod()){
                    continue;
                }
                $routeData = [
                    'ip' => $data->getClientIp(),
                    'route' => $routeMethod.$routeUrl
                ];
                $transformed = $this->transformer->make($routeData)->transform($routeData, $routeLimit, $routeTime);
                if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
                    $this->throttlers[$key] = $this->factory->make($transformed);
                }
            }
        }else{
            $transformed = $this->transformer->make($data)->transform($data, $limit, $time);
            if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
                $this->throttlers[$key] = $this->factory->make($transformed);
            }
        }
        return $this->throttlers[$key];
    }

    /**
     * Get the cache instance.
     *
     * @return \MuhamedDidovic\Throttle\Factories\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get the transformer instance.
     *
     * @codeCoverageIgnore
     *
     * @return \MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Dynamically pass methods to a new throttler instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        //print_r($parameters);die;
        return $this->get(...$parameters)->$method();
    }
}
