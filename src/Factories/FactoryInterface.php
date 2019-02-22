<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Factories;

use MuhamedDidovic\Throttle\Data;

/**
 * This is the throttler factory interface.
 *
 */
interface FactoryInterface
{
    /**
     * Make a new throttler instance.
     *
     * @param \MuhamedDidovic\Throttle\Data $data
     *
     * @return \MuhamedDidovic\Throttle\Throttlers\ThrottlerInterface
     */
    public function make(Data $data);
}
