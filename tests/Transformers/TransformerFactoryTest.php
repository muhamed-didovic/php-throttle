<?php

namespace MuhamedDidovic\Tests\Throttle\Transformers;

use GrahamCampbell\TestBench\AbstractTestCase;
use MuhamedDidovic\Throttle\Data;
use MuhamedDidovic\Throttle\Transformers\ArrayTransformer;
use MuhamedDidovic\Throttle\Transformers\LaravelTransformer;
use MuhamedDidovic\Throttle\Transformers\RequestTransformer;
use MuhamedDidovic\Throttle\Transformers\TransformerFactory;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;

/**
 * This is the transformer factory test class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class TransformerFactoryTest extends AbstractTestCase
{
    /**
     * @group laravel
     * @test
     */
    public function isLaravelRequest()
    {
        $factory = new TransformerFactory();
        
        $transformer = $factory->make($request = Mockery::mock(Request::class));
//        dd(1, $transformer);
        $this->assertInstanceOf(LaravelTransformer::class, $transformer);

        $request->shouldReceive('getClientIp')->once()->andReturn('123.123.123.123');
        $request->shouldReceive('path')->once()->andReturn('foobar');

        $this->assertInstanceOf(Data::class, $transformer->transform($request, 123, 321));
    }
    
    /**
     * @test
     */
    public function arrayInstance()
    {
        $factory = new TransformerFactory();
        $transformer = $factory->make($array = ['ip' => 'abc', 'route' => 'qwerty']);

        $this->assertInstanceOf(ArrayTransformer::class, $transformer);

        $this->assertInstanceOf(Data::class, $transformer->transform($array, 123, 321));
    }
    
    /**
     * @test
     */
    public function emptyArray()
    {
        $factory = new TransformerFactory();
        $transformer = $factory->make([]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The data array does not provide the required ip and route information.');

        $transformer->transform([]);
    }
    
    /**
     * @test
     */
    public function error()
    {
        $factory = new TransformerFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An array, object, or an instance of Illuminate\Http\Request or Zend_Controller_Request_Http was expected.');

        $factory->make(123);
    }
}
