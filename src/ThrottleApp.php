<?php

namespace MuhamedDidovic\Throttle;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use MuhamedDidovic\Throttle\Factories\CacheFactory;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;

class ThrottleApp
{
    private $app;
    private $throttle;
    private $config;

    public function __construct($config = []){

        $this->app = new Container();

        $this->setupConfig($config);
        $this->setupAppConfig();
        $this->setupCache();

        $this->registerFactory();
        $this->registerTransformer();
        $this->registerThrottle();

        $this->throttle = $this->app->make('throttle');
    }

    public function getThrottle(){
        return $this->throttle;
    }

    protected function setupCache()
    {
        $this->app['config'] = [
            'cache.default' => 'file',
            'cache.stores.file' => [
                'driver' => $this->config['cache.driver'],
                'path' => $this->config['cache.path']
            ]
        ];
        // To use the file cache driver we need an instance of Illuminate's Filesystem, also stored in the container
        $this->app['files'] = new Filesystem;
        $this->app->cache = new \Illuminate\Cache\CacheManager($this->app);
    }

    protected function setupConfig($inputs){

        $defaults = array(
            'cache.path'=> '/tmp',
            'cache.driver' => 'file',
            'limit' => 100,
            'time' => 1,
            'config.path' => __DIR__.'/../config/throttle.php'
        );

        $this->config = array_merge($defaults, $inputs);
    }

    protected function setupAppConfig()
    {
        $config = new \Illuminate\Config\Repository(require $this->config['config.path']);
        $this->app->config = $config;
    }

    /**
     * Register the factory class.
     *
     * @return void
     */
    protected function registerFactory()
    {
        $this->app->singleton('throttle.factory', function (Container $app) {
            $cache = $app->cache->driver('file');
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
            $factory = $app['throttle.factory'];
            $transformer = $app['throttle.transformer'];
            return new Throttle($factory, $transformer, $this->config);
        });
        $this->app->alias('throttle', Throttle::class);
    }
}
