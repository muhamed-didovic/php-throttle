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
 *
 * @method bool attempt(array|\Illuminate\Http\Request $data, int $limit, int $time)
 * @method \MuhamedDidovic\Throttle\Throttlers\ThrottlerInterface hit(array|\Illuminate\Http\Request $data, int $limit, int $time)
 * @method \MuhamedDidovic\Throttle\Throttlers\ThrottlerInterface clear(array|\Illuminate\Http\Request $data, int $limit, int $time)
 * @method int count(array|\Illuminate\Http\Request $data, int $limit, int $time)
 * @method bool check(array|\Illuminate\Http\Request $data, int $limit, int $time)
 *
 */
class Throttle
{
    /**
     * The cached throttler instances.
     *
     * @var \MuhamedDidovic\Throttle\Throttlers\ThrottlerInterface[]
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
     * @param int   $limit
     * @param int   $time
     *
     * @return \MuhamedDidovic\Throttle\Throttlers\ThrottlerInterface
     */
    public function get($data, $limit = 10, $time = 60)
    {
        $transformed = $this->transformer->make($data)->transform($data, $limit, $time);

        if (!array_key_exists($key = $transformed->getKey(), $this->throttlers)) {
            $this->throttlers[$key] = $this->factory->make($transformed);
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
        return $this->get(...$parameters)->$method();
    }
}
