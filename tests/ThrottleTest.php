<?php

namespace MuhamedDidovic\Tests\Throttle;

use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use MuhamedDidovic\Throttle\Data;
use MuhamedDidovic\Throttle\Factories\CacheFactory;
use MuhamedDidovic\Throttle\Throttle;
use MuhamedDidovic\Throttle\ThrottleApp;
use MuhamedDidovic\Throttle\Throttlers\CacheThrottler;
use MuhamedDidovic\Throttle\Transformers\ArrayTransformer;
use MuhamedDidovic\Throttle\Transformers\TransformerFactoryInterface;
use Mockery;

/**
 * This is the throttle test class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class ThrottleTest extends AbstractTestBenchTestCase
{
    
    /**
     * @return array
     */
    protected function getThrottle()
    {
        
        $factory = Mockery::mock(CacheFactory::class);
        
        $data = ['ip' => '127.0.0.1', 'route' => 'http://laravel.com/'];
        
        $throttler = Mockery::mock(CacheThrottler::class);
        
        $trans = Mockery::mock(ArrayTransformer::class);
        
        $transformer = Mockery::mock(TransformerFactoryInterface::class);
        
        $transformer
            ->shouldReceive('make')
            ->with($data)
            ->andReturn($trans);
        
        $trans
            ->shouldReceive('transform')
            ->with($data, 12, 123)
            ->andReturn($transformed = new Data('127.0.0.1', 'GEThttp://laravel.com/', 12, 123));
        
        $config = [
            "cache.path"   => "/tmp",
            "cache.driver" => "file",
            "limit"        => 10,
            "time"         => 1,
            "config.path"  => "/home/vagrant/projects/throttle/src/../config/throttle.php",
            "routes"       => [
                0 => [
                    "url"   => "http://laravel.com/",
                    "limit" => 12,
                    "time"  => 123,
                ],
            ],
        ];
        
        $throttle    = new Throttle($factory, $transformer, $config);
        $throttleApp = (new ThrottleApp($config))->setThrottle($throttle);
        
        //dd('12123', $throttleApp->getFactoryFromThrottle()->shouldReceive('make'));
        $throttleApp
            //->getFactory()
            ->getFactoryFromThrottle()
            ->shouldReceive('make')
            ->once()
            ->with($transformed)
            ->andReturn($throttler);
        
        //dd('***',compact('throttle', 'throttler', 'data', 'factory'));
        return compact('throttle', 'throttler', 'data', 'factory');
    }
    
    /**
     * @test
     */
    public function make()
    {
        extract($this->getThrottle());
        
        /** @var Throttle $throttle */
        $return = $throttle->get($data, 12, 123);
        
        $this->assertInstanceOf(CacheThrottler::class, $return);
    }
    
    /**
     * @test
     */
    public function cache()
    {
        extract($this->getThrottle());
        
        for ($i = 0; $i < 3; $i++) {
            $return = $throttle->get($data, 12, 123);
            $this->assertInstanceOf(CacheThrottler::class, $return);
        }
    }
    
    /**
     * @test
     */
    public function call()
    {
        extract($this->getThrottle());
        
        $throttler->shouldReceive('hit')->once()->andReturnSelf();
        
        $return = $throttle->hit($data, 12, 123);
        
        $this->assertInstanceOf(CacheThrottler::class, $return);
    }
}