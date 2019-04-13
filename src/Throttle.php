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
     * @var bool
     */
    public $withMiddleware = false;
    /**
     * The cached throttler instances.
     *
     * @var Throttlers\ThrottlerInterface[]
     */
    protected $throttlers = [];
    
    /**
     * The factory instance.
     *
     * @var FactoryInterface
     */
    protected $factory;
    
    /**
     * The factory instance.
     *
     * @var TransformerFactoryInterface
     */
    protected $transformer;
    
    /**
     * @var
     */
    protected $config;
    
    /**
     * Throttle constructor.
     * @param FactoryInterface            $factory
     * @param TransformerFactoryInterface $transformer
     * @param                             $config
     */
    public function __construct(FactoryInterface $factory, TransformerFactoryInterface $transformer, $config)
    {
        $this->factory     = $factory;
        $this->transformer = $transformer;
        $this->config      = $config;
    }
    
    /**
     * Laravel middleware will be used
     */
    public function enableMiddleware()
    {
        $this->withMiddleware = true;
    }
    
    /**
     * Get a new throttler.
     *
     * @param mixed $data
     *
     * @return Throttlers\ThrottlerInterface
     * @internal param null $routes
     */
    public function get($data, $limit = 10, $time = 60)
    {
        if ($this->config['routes'] && !$this->withMiddleware) {
            
            foreach ($this->config['routes'] as $route) {
                if (empty($route) || empty($route['url'])) {
                    continue;
                }
                
                $routeUrl    = $route['url'];
                $routeLimit  = !empty($route['limit']) ? $route['limit'] : $this->config['limit'];
                $routeTime   = !empty($route['time']) ? $route['time'] : $this->config['time'];
                $routeMethod = !empty($route['method']) ? $route['method'] : 'GET';
                
                $transformed = $this
                    ->transformer
                    ->make($data)
                    ->transform($data, $routeLimit, $routeTime);
                
                if (!$transformed || $routeMethod . $routeUrl != $transformed->getRoute()) {
                    continue;
                }
                
                if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
                    $this->throttlers[$key] = $this->factory->make($transformed);
                }
            }
        } else {
            
            $transformed = $this
                ->transformer
                ->make($data)
                ->transform(
                    $data,
                    $this->withMiddleware ? $limit : $this->config['limit'], //check is Laravel used
                    $this->withMiddleware ? $time : $this->config['time'] //check is Laravel used
                //!empty($this->config['limit']) ? $this->config['limit'] : 10,
                //!empty($this->config['time']) ? $this->config['time'] : 1
                );
            
            if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
                $this->throttlers[$key] = $this->factory->make($transformed);
            }
        }
        
        return $this->throttlers[$key];
    }
    
    /**
     * Get the cache instance.
     *
     * @return FactoryInterface
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
     * @return TransformerFactoryInterface
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
        return $this->get(...$parameters) ? $this->get(...$parameters)->$method() : 1;
    }
}
