<?php

namespace MuhamedDidovic\Throttle\Transformers;

use MuhamedDidovic\Throttle\Data;

/**
 * This is the request transformer class.
 *
 */
class ZendRequestTransformer implements TransformerInterface
{
    /**
     * Transform the data into a new data instance.
     *
     * @param  $data
     * @param  $limit
     * @param  $time
     *
     * @return Data
     */
    public function transform($data, $limit = 10, $time = 60)
    {
        return new Data((string)$data->getClientIp(), (string)$data->getMethod() . $data->getPathInfo(), (int)$limit, (int)$time);
    }
}
