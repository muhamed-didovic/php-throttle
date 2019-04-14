<?php

namespace MuhamedDidovic\Tests\Throttle\Functional;


use MuhamedDidovic\Tests\Throttle\AbstractTestCase;
use MuhamedDidovic\Throttle\Http\Middleware\ThrottleMiddleware;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Http\Request;
use Mockery;
use MuhamedDidovic\Throttle\Throttle;
use MuhamedDidovic\Throttle\ThrottleApp;
use MuhamedDidovic\Throttle\Transformers\LaravelTransformer;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;

/**
 * This is the middleware test class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class MiddlewareTest extends AbstractTestCase
{
    
    /**
     * Setup the application environment.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        
        $app->config->set('throttle.driver', 'array');
    
    }
    
    /**
     * @after
     */
    public function tearDown()
    {
        $this->app->cache->driver('array')->flush();
    }
    
    /**
     * @group mid
     * @test
     */
    public function BasicMiddlewareSuccess()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class, function () {
            return 'Why herro there!';
        }]);
        
        $this->hit(10);
    }
    
    /**
     * @test
     */
    public function BasicMiddlewareFailure()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class, function () {
            return 'Why herro there!';
        }]);
        
        $this->expectException(TooManyRequestsHttpException::class);
        
        $this->hit(11);
    }
    
    /**
     * @test
     */
    public function CustomLimitSuccess()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class.':5', function () {
            return 'Why herro there!';
        }]);
        
        $this->hit(5);
    }
    
    /**
     * @group rate
     * @test
     */
    public function CustomLimitFailure()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class.':5', function () {
            return 'Why herro there!';
        }]);
        
        $this->expectException(TooManyRequestsHttpException::class);
        
        $this->hit(6);
    }
    
    /**
     * @test
     */
    public function CustomTimeSuccess()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class.':3,5', function () {
            return 'Why herro there!';
        }]);
        
        $this->hit(3, 300);
    }
    
    /**
     * @test
     */
    public function CustomTimeFailure()
    {
        $this->app->router->get('throttle-test-route', ['middleware' => ThrottleMiddleware::class.':3,5', function () {
            return 'Why herro there!';
        }]);
        
        $this->expectException(TooManyRequestsHttpException::class);
        
        $this->hit(4, 300);
    }
    
    protected function hit($times, $time = 3600)
    {
        for ($i = 0; $i < $times - 1; $i++) {
            $this->wrappedCall('GET', 'throttle-test-route');
        }
        
        try {
            $this->wrappedCall('GET', 'throttle-test-route');
        } catch (TooManyRequestsHttpException $e) {
            $this->assertSame('Rate limit exceeded.', $e->getMessage());
            $this->assertSame($time, $e->getHeaders()['Retry-After']);
            
            throw $e;
        }
    }
    
    protected function wrappedCall($method, $uri)
    {
        $response = $this->call($method, $uri);
        
        if ($ex = $response->exception) {
            throw $ex;
        }
        
        $this->assertSame(200, $response->status());
    }
}
