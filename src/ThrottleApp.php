<?php

namespace MuhamedDidovic\Throttle;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use MuhamedDidovic\Throttle\Factories\CacheFactory;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;

/**
 * Class ThrottleApp
 * @package MuhamedDidovic\Throttle
 */
class ThrottleApp
{
    /**
     * @var Container
     */
    private $app;
    /**
     * @var Throttle
     */
    private $throttle;
    /**
     * @var
     */
    private $config;
    
    /**
     * ThrottleApp constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        
        $this->app = new Container();
        
        $this->setupConfig($config);
        $this->setupAppConfig();
        $this->setupCache();
        
        $this->registerFactory();
        $this->registerTransformer();
        $this->registerThrottle();
        
        $this->throttle = $this->app->make('throttle');
    }
    
    /**
     * @return mixed
     */
    public function getThrottle()
    {
        return $this->throttle;
    }
    
    /**
     * @return self
     */
    public function setThrottle($throttle)
    {
        $this->throttle = $throttle;
        
        return $this;
    }
    
    /**
     * Get factory from throttle
     * @return mixed
     */
    public function getFactoryFromThrottle()
    {
        return $this->throttle->getFactory();
    }
    
    
    /**
     * @param $configOverrides
     */
    protected function setupConfig($configOverrides)
    {
        //        $defaults = array(
        //            //            'cache.path'   => '/tmp',
        //            //            'cache.driver' => 'file',
        //            //            'limit'        => 100,
        //            //            'time'         => 1,
        //            'config.path' => __DIR__ . '/../config/throttle.php',
        //        );
        
        $this->config = array_merge(require __DIR__ . '/../config/throttle.php', $configOverrides);
    }
    
    /**
     *
     */
    protected function setupAppConfig()
    {
        $config            = new Repository($this->config);
        $this->app->config = $config;
    }
    
    /**
     *
     */
    protected function setupCache()
    {
        $this->app->config->set('cache.default', $this->app->config->get('driver'));
        $this->app->config->set('cache.stores.file', [
            'driver' => $this->config['cache.driver'],
            'path'   => $this->app->config->get('driver')//$this->config['cache.path'],
        ]);
        //        $this->app['config'] = [
        //            'cache.default'     => $this->app->config->get('driver'),
        //            'cache.stores.file' => [
        //                'driver' => $this->config['cache.driver'],
        //                'path'   => $this->config['cache.path'],
        //            ],
        //        ];
        
        // To use the file cache driver we need an instance of Illuminate's Filesystem, also stored in the container
        $this->app['files'] = new Filesystem;
        $this->app->cache   = new CacheManager($this->app);
        
    }
    
    /**
     * Register the factory class.
     *
     * @return void
     */
    protected function registerFactory()
    {
        $this->app->singleton('throttle.factory', function (Container $app) {
            //$cache = $app->cache->driver('file');
            $cache = $app->cache->driver($app->config->get('driver'));
            
            return new CacheFactory($cache);
        });
        $this->app->alias('throttle.factory', CacheFactory::class);
        $this->app->alias('throttle.factory', FactoryInterface::class);
    }
    
    /**
     * Register the transformer class.
     *
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->singleton('throttle.transformer', function () {
            return new TransformerFactory();
        });
        $this->app->alias('throttle.transformer', TransformerFactory::class);
        $this->app->alias('throttle.transformer', TransformerFactoryInterface::class);
    }
    
    /**
     * Register the throttle class.
     *
     * @return void
     */
    protected function registerThrottle()
    {
        $this->app->singleton('throttle', function (Container $app) {
            $factory     = $app['throttle.factory'];
            $transformer = $app['throttle.transformer'];
            
            return new Throttle($factory, $transformer, $this->config);
        });
        $this->app->alias('throttle', Throttle::class);
    }
}
