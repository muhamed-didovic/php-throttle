<?php

//declare(strict_types=1);


namespace MuhamedDidovic\Throttle;

use MuhamedDidovic\Throttle\Factories\FactoryInterface;
use MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface;

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

    protected $config;

    public function __construct(FactoryInterface $factory, TransformerFactoryInterface $transformer, $config)
    {
        $this->factory = $factory;
        $this->transformer = $transformer;
        $this->config = $config;
    }

    /**
     * Get a new throttler.
     *
     * @param mixed $data
     *
     * @return Throttlers\ThrottlerInterface
     * @internal param null $routes
     */
    public function get($data)
    {
        if($this->config['routes']){
            foreach($this->config['routes'] as $route){
                if(empty($route) || empty($route['url'])) continue;

                $routeUrl = $route['url'];
                $routeLimit = !empty($route['limit'])?$route['limit']: $this->config['limit'];
                $routeTime = !empty($route['time'])?$route['time']:$this->config['time'];
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
            $transformed = $this->transformer->make($data)->transform($data, $this->config['limit'], $this->config['time']);
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
        return $this->get(...$parameters)?$this->get(...$parameters)->$method():1;
    }
}
