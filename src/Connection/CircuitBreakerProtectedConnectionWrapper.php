<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;

use Doctrine\Common\Cache\FilesystemCache;
use Eljam\CircuitBreaker\Breaker;
use Eljam\CircuitBreaker\Event\CircuitEvents;
use Symfony\Component\EventDispatcher\Event;

use Fusio\Adapter\Webfantize\Connection\CircuitBreakerConnection;
use Fusio\Engine\ConnectorInterface;

class CircuitBreakerProtectedConnectionWrapper 
//	extends Connection 
	implements ConnectionInterface
{
	 protected $connector;
	
     public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
 

 

    public function getName():string
    {
        return 'CircuitBreakerProtectedConnectionWrapper';
    }



    public function getConnection(ParametersInterface $configuration) :mixed
    { 
		$FilesystemCache = $this->connector->getConnection($configuration->get('FilesystemCache'));
		$Connection = $this->connector->getConnection($configuration->get('connection'));

		$breaker = new Breaker($this->getName().'-'.$this->getId(), [	
			'ignore_exceptions' => false
		], $FilesystemCache);

		$connection = new CircuitBreakerConnection($breaker, $Connection);

		return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory):void
    {

	/*
          $builder->add($elementFactory->newInput('circuit.breaker.name',
												  'Name', 'text', 'The Name of the CircuitBreakerProtectedConnectionWrapper'));
		*/
		
         $builder->add($elementFactory->newConnection('FilesystemCache',
			 \Fusio\Adapter\Webfantize\Connection\FilesystemCache::class, 
			 'The FilesystemCache used to store the CircuitBreaker Data',
			 null));


          $builder->add($elementFactory->newConnection('connection',
												  \Fusio\Engine\Model\Connection::class, 
												  'The connection to wrap into the circuit-breaker',
													 null));
   
    }
}
