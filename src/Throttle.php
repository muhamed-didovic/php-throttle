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
     * Get a new throttler.
     *
     * @param mixed $data
     *
     * @return Throttlers\ThrottlerInterface
     * @internal param null $routes
     */
    public function get($data)
    {
       
        if ($this->config['routes']) {
            
            //ensure we have time for routes
            if (empty($this->config['time'])){
                return;
            }
            
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
                    ->transform($data, $routeLimit, $routeTime); // 10, 1 - 12, 123
                
                if (!$transformed || $routeMethod . $routeUrl != $transformed->getRoute()) {
                    continue;
                }
                
                //dd('a', $transformed);
                if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
                    $this->throttlers[$key] = $this->factory->make($transformed);
                }
            }
        } else {
            
            $transformed = $this
                ->transformer
                ->make($data)
                ->transform($data, $this->config['limit'], $this->config['time']);
            
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
