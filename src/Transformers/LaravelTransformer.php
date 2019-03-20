<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Transformers;

use MuhamedDidovic\Throttle\Data;
use InvalidArgumentException;

class LaravelTransformer implements TransformerInterface
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
        return new Data((string) $data->ip(), (string) $data->method().$data->path(), (int) $limit, (int) $time);
    }
}
