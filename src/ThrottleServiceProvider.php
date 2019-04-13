<?php

namespace MuhamedDidovic\Throttle;

use MuhamedDidovic\Throttle\Factories\CacheFactory;
use MuhamedDidovic\Throttle\Factories\FactoryInterface;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;
use MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the throttle service provider class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class ThrottleServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath($raw = __DIR__.'/../config/throttle.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('throttle.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('throttle');
        }

        $this->mergeConfigFrom($source, 'throttle');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFactory();
        $this->registerTransformer();
        $this->registerThrottle();
    }

    /**
     * Register the factory class.
     *
     * @return void
     */
    protected function registerFactory()
    {
        $this->app->singleton('throttle.factory', function (Container $app) {
            $cache = $app->cache->driver($app->config->get('throttle.driver'));

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
//dd(require __DIR__ . '/../config/throttle.php');
            return new Throttle($factory, $transformer, require __DIR__ . '/../config/throttle.php');
        });

        $this->app->alias('throttle', Throttle::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'throttle',
            'throttle.factory',
            'throttle.transformer',
        ];
    }
}
