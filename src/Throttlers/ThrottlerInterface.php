<?php

//declare(strict_types=1);

namespace MuhamedDidovic\Throttle\Throttlers;

/**
 * This is the throttler interface class.
 *
 */
interface ThrottlerInterface
{
    /**
     * Rate limit access to a resource.
     *
     * @return bool
     */
    public function attempt();

    /**
     * Hit the throttle.
     *
     * @return $this
     */
    public function hit();

    /**
     * Clear the throttle.
     *
     * @return $this
     */
    public function clear();

    /**
     * Get the throttle hit count.
     *
     * @return int
     */
    public function count();

    /**
     * Check the throttle.
     *
     * @return bool
     */
    public function check();
}
