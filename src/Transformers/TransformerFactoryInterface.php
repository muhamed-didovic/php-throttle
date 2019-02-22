<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Transformers;

/**
 * This is the transformer factory interface.
 *
 */
interface TransformerFactoryInterface
{
    /**
     * Make a new transformer instance.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException
     *
     * @return \MuhamedDidovic\Throttle\Transformers\TransformerInterface
     */
    public function make($data);
}
