<?php

//declare(strict_types=1);


namespace MuhamedDidovic\Throttle\Transformers;

/**
 * This is the transformer interface.
 *
 */
interface TransformerInterface
{
    /**
     * Transform the data into a new data instance.
     *
     * @param array|\Illuminate\Http\Request $data
     * @param int                            $limit
     * @param int                            $time
     *
     * @return \MuhamedDidovic\Throttle\Data
     */
    public function transform($data, $limit = 10, $time = 60);
}
