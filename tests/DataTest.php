<?php

namespace MuhamedDidovic\Tests\Throttle;

use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use MuhamedDidovic\Throttle\Data;

/**
 * This is the data test class.
 *
 * @author Muhamed Didovic <muhamed.didovic@gmail.com>
 */
class DataTest extends AbstractTestBenchTestCase
{
    /**
     * @return Data
     */
    protected function getData()
    {
        return new Data('127.0.0.1', 'https://google.co.uk/', 123, 321);
    }
    
    /**
     * @test
     */
    public function getIp()
    {
        $data = $this->getData();

        $this->assertSame('127.0.0.1', $data->getIp());
    }
    
    /**
     * @test
     */
    public function getRoute()
    {
        $data = $this->getData();

        $this->assertSame('https://google.co.uk/', $data->getRoute());
    }
    
    /**
     * @test
     */
    public function getLimit()
    {
        $data = $this->getData();

        $this->assertSame(123, $data->getLimit());
    }
    
    /**
     * @test
     */
    public function getTime()
    {
        $data = $this->getData();

        $this->assertSame(321, $data->getTime());
    }
    
    /**
     * @test
     */
    public function getKey()
    {
        $data = $this->getData();

        $this->assertSame('9fa39d579031694fbc8e2931aa354df18883e5f2', $data->getKey());
    }
}
