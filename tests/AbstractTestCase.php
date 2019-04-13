<?php

namespace MuhamedDidovic\Tests\Throttle;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use MuhamedDidovic\Throttle\ThrottleServiceProvider;

/**
 * This is the abstract test case class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
abstract class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return ThrottleServiceProvider::class;
    }
}
