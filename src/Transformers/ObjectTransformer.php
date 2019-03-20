<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Transformers;

use MuhamedDidovic\Throttle\Data;
use InvalidArgumentException;

class ObjectTransformer implements TransformerInterface
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
        if (!empty($data->ip) && !empty($data->route)) {
            return new Data((string) $data->ip, (string) $data->route, (int) $limit, (int) $time);
        }

        throw new InvalidArgumentException('The data object does not provide the required ip and route information.');
    }
}
