<?php

//declare(strict_types=1);

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
     * @param \Illuminate\Http\Request $data
     * @param                       $limit
     * @param                       $time
     *
     * @return \MuhamedDidovic\Throttle\Data
     */
    public function transform($data, $limit = 10, $time = 60)
    {
        return new Data((string) $data->getClientIp(), (string) $data->getPathInfo(), (int) $limit, (int) $time);
    }
}
