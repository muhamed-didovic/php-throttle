<?php

//declare(strict_types=1);


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
     * @throws \InvalidArgumentException
     *
     * @return \MuhamedDidovic\Throttle\Transformers\TransformerInterface
     */
    public function make($data)
    {
        if ($data instanceof Request) {
            return new ObjectTransformer();
        } else if($data instanceof Zend_Controller_Request_Http){
            return new ZendRequestTransformer();
        } else if(is_object($data)){
            return new ObjectTransformer();
        }

        if (is_array($data)) {
            return new ArrayTransformer();
        }

        throw new InvalidArgumentException('An array, or an instance of Illuminate\Http\Request was expected.');
    }
}
