<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Transformers;

use MuhamedDidovic\Throttle\Data;
use InvalidArgumentException;

/**
 * This is the array transformer class.
 *
 */
class ArrayTransformer implements TransformerInterface
{
    /**
     * Transform the data into a new data instance.
     *
     * @param array $data
     * @param int   $limit
     * @param int   $time
     *
     * @throws \InvalidArgumentException
     *
     * @return \MuhamedDidovic\Throttle\Data
     */
    public function transform($data, $limit = 10, $time = 60)
    {
        if (($ip = array_get($data, 'ip')) && ($route = array_get($data, 'route'))) {
            return new Data((string) $ip, (string) $route, (int) $limit, (int) $time);
        }

        throw new InvalidArgumentException('The data array does not provide the required ip and route information.');
    }
}
