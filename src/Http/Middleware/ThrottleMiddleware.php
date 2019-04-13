<?php

namespace MuhamedDidovic\Throttle\Http\Middleware;

use Closure;
use MuhamedDidovic\Throttle\Throttle;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * This is the throttle middleware class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class ThrottleMiddleware
{
    /**
     * The throttle instance.
     *
     * @var \MuhamedDidovic\Throttle\Throttle
     */
    protected $throttle;

    /**
     * Create a new throttle middleware instance.
     *
     * @param \MuhamedDidovic\Throttle\Throttle $throttle
     *
     * @return void
     */
    public function __construct(Throttle $throttle)
    {
        $this->throttle = $throttle;
        $this->throttle->enableMiddleware();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param int                      $limit
     * @param int                      $time
     *
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $limit = 10, $time = 60)
    {
        if (!$this->throttle->attempt($request, (int) $limit, (int) $time)) {
            throw new TooManyRequestsHttpException($time * 60, 'Rate limit exceeded.');
        }

        return $next($request);
    }
}
