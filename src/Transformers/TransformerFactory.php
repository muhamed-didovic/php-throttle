<?php

namespace MuhamedDidovic\Throttle\Transformers;

use InvalidArgumentException;
use Zend_Controller_Request_Http;
use Illuminate\Http\Request;

/**
 * This is the transformer factory class.
 *
 */
class TransformerFactory implements TransformerFactoryInterface
{
    /**
     * Make a new transformer instance.
     *
     * @param mixed $data
     *
     * @throws InvalidArgumentException
     *
     * @return TransformerInterface
     */
    public function make($data)
    {
        
        if ($data instanceof Request) {
            return new LaravelTransformer();
        }
        
        if($data instanceof Zend_Controller_Request_Http){
            return new ZendRequestTransformer();
        }
        
        if(is_object($data)){
            return new ObjectTransformer();
        }
        
        if (is_array($data)) {
            return new ArrayTransformer();
        }
        
        throw new InvalidArgumentException('An array, object, or an instance of Illuminate\Http\Request or Zend_Controller_Request_Http was expected.');
    }
}
