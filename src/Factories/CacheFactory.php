<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Factories;

use MuhamedDidovic\Throttle\Data;
use MuhamedDidovic\Throttle\Throttlers\CacheThrottler;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Repository;

/**
 * This is the cache throttler factory class.
 *
 */
class CacheFactory implements FactoryInterface
{
    /**
     * The cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Contracts\Cache\Repository $cache
     *
     * @return void
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Make a new cache throttler instance.
     *
     * @param \MuhamedDidovic\Throttle\Data $data
     *
     * @return \MuhamedDidovic\Throttle\Throttlers\CacheThrottler
     */
    public function make(Data $data)
    {
        return new CacheThrottler($this->cache->getStore(), $data->getKey(), $data->getLimit(), $data->getTime());
    }

    /**
     * Get the cache instance.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getCache()
    {
        return $this->cache;
    }
}
